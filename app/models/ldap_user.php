<?php
class LdapUser extends AppModel
{
	var $name 		= 'LdapUser';
	var $useTable 	= false;
	var $primaryKey = 'samaccountname';
	var $host 		= 'lldc01.luxurylink.com';
	var $backupHost		= 'manila.luxurylink.com';
	var $port 		= 389;
	var $baseDn 	= 'OU=ServiceAccounts,DC=luxurylink,DC=com';
	var $userBaseDn = 'OU=LuxuryLinkUser,DC=luxurylink,DC=com';
	var $user 		= 'luxury';
	var $pass		= 'traveler';
	var $displayField = 'displayname';

    var $ds;

    function __construct()
    {
        parent::__construct();
        $this->ds = ldap_connect($this->host, $this->port);
	if (!$this->ds) {
		$this->ds = ldap_connect($this->backupHost, $this->port);
	}
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
            $result = $this->afterFind($result);
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
            $convert = $this->afterFind($convert);
            return $convert[0];
        }
    }

    function auth($samaccountname, $password)
    {
        if (trim($password) == '') {
            return false;
        }
        $result = $this->findAll('samaccountname', $samaccountname);
        $result = $this->afterFind($result);
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
     
     function afterFind($data) {
         if (is_array($data)) {
         foreach ($data as $k => $v) {
             if (isset($v['LdapUser']['memberof']) && is_array($v['LdapUser']['memberof'])) {
             foreach ($v['LdapUser']['memberof'] as $v2) {
                 $group = substr($v2, 3, strpos($v2, ',')-3);
                 
                 if (trim($group)) {
                     $groups[] = trim($group);
                 }
             }
             $data[$k]['LdapUser']['groups'] = $groups;
            }
         }
        }
        
        return $data;
     }
}
?>
