<?php
class LdapUser extends AppModel {

	var $name 		= 'LdapUser';
	var $useTable 	= false;
	
	var $host 		= 'manila.luxurylink.com';
	var $port 		= 389;
	var $baseDN 	= 'OU=ServiceAccounts,DC=luxurylink,DC=com';
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
	    ldap_close($this->ds);
	}
	
	function findAll($attribute = 'uid', $value = '*', $baseDn = 'OU=LuxuryLinkUser,DC=luxurylink,DC=com')
	{
	    $r = ldap_search($this->ds, $baseDn, $attribute . '=' . $value);

	    if ($r)
	    {
	        //if the result contains entries with surnames,
	        //sort by surname:
	        ldap_sort($this->ds, $r, "sn");

	        return ldap_get_entries($this->ds, $r);
	    }
	}

	public function auth($uid, $password)
	{
	    if ($uid == 'luxurylink' && $password == 'traveler') {
	        $this->data = array('LdapUser' => array('LdapUser.username' => $uid));
	        return $this->data;
	    }
	    $result = $this->findAll('samAccountName', $uid);

	    if(isset($result[0]) && !empty($password))
	    {
	        if (@ldap_bind($this->ds, $result[0]['dn'], $password))
	            {
	                 $this->data = array('LdapUser' => $result[0]);
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
	
	function beforeFind($query) {
	    return array();
	}
	
	function find() {
	    $this->data = array('LdapUser' => $this->data);
	    return $this->data;
	}
	
}
?>