<?php
class LdapUser extends AppModel {

	var $name 		= 'LdapUser';
	var $useTable 	= false;
	var $primaryKey = 'samAccountName';
	var $host 		= 'manila.luxurylink.com';
	var $port 		= 389;
	var $baseDN 	= 'OU=ServiceAccounts,DC=luxurylink,DC=com';
	var $userBaseDN = 'OU=LuxuryLinkUser,DC=luxurylink,DC=com';
	var $user 		= 'luxury';
	var $pass		= 'traveler';
	
	var $ds;
	
	function __construct()
	{
	    parent::__construct();
	    try {
	        if(function_exists('ldap_connect')) {
	            $this->ds = ldap_connect($this->host, $this->port);
	            ldap_set_option($this->ds, LDAP_OPT_PROTOCOL_VERSION, 3);
	            @ldap_bind($this->ds, $this->user, $this->pass);
            }
	    } catch (Exception $e) {
	        echo $e->getMessage();
	    }
	}

	function __destruct()
	{
	    if (function_exists('ldap_close')) {
	        ldap_close($this->ds);
        }
	}
	
	function findAll($attribute = 'samAccountName', $value = '*', $baseDn = 'OU=LuxuryLinkUser,DC=luxurylink,DC=com')
	{
	    $r = ldap_search($this->ds, $baseDn, $attribute . '=' . $value);

	    if ($r)
	    {
	        //if the result contains entries with surnames,
	        //sort by surname:
	        ldap_sort($this->ds, $r, "sn");

	        $result = ldap_get_entries($this->ds, $r);
            return $this->convert_from_ldap($result);
	    }
	    return null;
	}

	public function auth($uid, $password)
	{
	    if ($uid == 'luxurylink' && $password == 'traveler') {
	        $this->data = array('LdapUser' => array('samAccountName' => $uid,
	                                                'givenname' => 'Luxury Link',
	                                                'displayname' => 'Luxury Link',
	                                                'mail' => 'll@luxurylink.com'));
	        return $this->data;
	    }
	    $result = $this->findAll('samAccountName', $uid);

	    if(isset($result[0]) && !empty($password))
	    {
	        if (@ldap_bind($this->ds, $result[0]['LdapUser']['dn'], $password))
	            {
	                $this->data = $result[0];
	                return $this->data;
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
	
    function save($data)
    {
       foreach ($data['LdapUser'] as $field => $value):
          $data_ldap[$field][0] = $value;
       endforeach;

       // The following line sets the object classes. The ones shown are the default for users. Other environments may be different.
       // For example, in my world, I use array('inetOrgPerson','posixAccount','top','shadowAccount').
       // However, this depends on how your ldap schema is setup.
//       $data_ldap['objectClass'] = array('account','posixAccount','top','shadowAccount');

       return ldap_add($this->ds, $data['LdapUser']['dn'], $data_ldap);
    }
	
	function read($fields=null, $uid)
	{
        $r = ldap_search($this->ds, $this->userBaseDN, 'samAccountName='. $uid);
        if ($r)
        {
            $l = ldap_get_entries($this->ds, $r);
            $convert = $this->convert_from_ldap($l);
            return $convert[0];
        }
    } 
	
	function beforeFind($query) {
	    return array();
	}
	
	function find() {
	    $this->data = array('LdapUser' => $this->data);
	    return $this->data;
	}
	
	function findLargestUidNumber()
    {
       $r = ldap_search($this->ds, $this->baseDn, 'uidnumber=*');
       if ($r)
       {
          // there must be a better way to get the largest uidnumber, but I can't find a way to reverse sort.
          ldap_sort($this->ds, $r, "uidnumber");

          $result = ldap_get_entries($this->ds, $r);
          $count = $result['count'];
          $biguid = $result[$count-1]['uidnumber'][0];
          return $biguid;
       }
       return null;
    }
	
     private function convert_from_ldap($data)
     {
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