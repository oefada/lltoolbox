<?php
class LdapUser extends AppModel
{
	var $name 		= 'LdapUser';
	var $useTable 	= false;
	var $primaryKey = 'samaccountname';
	var $host 		= 'manila.luxurylink.com';
	var $port 		= 389;
	var $baseDn 	= 'OU=ServiceAccounts,DC=luxurylink,DC=com';
	var $userBaseDn = 'OU=LuxuryLinkUser,DC=luxurylink,DC=com';
	var $user 		= 'luxury';
	var $pass		= 'traveler';

    var $ds;

    function __construct()
    {
        parent::__construct();
        $this->ds = ldap_connect($this->host, $this->port);
        ldap_set_option($this->ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        if ($this->user) {
            ldap_bind($this->ds, $this->user, $this->pass);
        } else {
            //Do an anonymous bind.
            ldap_bind($this->ds);
        }
    }

    function __destruct()
    {
        ldap_close($this->ds);
    }

    function findAll($attribute = 'samaccountname', $value = '*', $baseDn = '')
    {
        if (!$baseDn) {
            $baseDn = $this->userBaseDn;
        }
        $r = ldap_search($this->ds, $baseDn, $attribute . '=' . $value);
        if ($r)
        {
            ldap_sort($this->ds, $r, "sn");
            
            $result = ldap_get_entries($this->ds, $r);
            return $this->convert_from_ldap($result);
        }
        return null;
    }

    function read($fields=null, $samaccountname)
    {
        $r = ldap_search($this->ds, $this->userBaseDn, 'samaccountname='. $samaccountname);
        if ($r)
        {
            $l = ldap_get_entries($this->ds, $r);
            $convert = $this->convert_from_ldap($l);
            return $convert[0];
        }
    }

    function auth($samaccountname, $password)
    {
        if (trim($password) == '') {
            return false;
        }
        $result = $this->findAll('samaccountname', $samaccountname);

        if(isset($result[0]))
        {
            if (@ldap_bind($this->ds, $result[0]['LdapUser']['dn'], $password))
            {
                return $result;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    private function convert_from_ldap($data)
    {
        $final = false;
      foreach ($data as $key => $row):
         if($key === 'count') continue;

         foreach($row as $key1 => $param):
            if(!is_numeric($key1)) {
                if (!is_array($param)) {
                    $final[$key]['LdapUser'][$key1] = $param;
                }
                continue;
           }
            if($row[$param]['count'] === 1)
               $final[$key]['LdapUser'][$param] = $row[$param][0];
            else
            {
               if (!is_array($row[$param])) {
                   $final[$key1] = $param;
               }
               foreach($row[$param] as $key2 => $item):
                  if($key2 === 'count') continue;
                  $final[$key]['LdapUser'][$param][] = $item;
               endforeach;
            }
         endforeach;
      endforeach;
      return $final;
     }
}
?>