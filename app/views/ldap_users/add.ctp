<h1>New LDAP User</h1>
<? if(isset($ldap_users['LdapUser']['uidnumber'])) $newuid = $ldap_users['LdapUser']['uidnumber'] ?>
<form action="<?php echo $html->url('/ldap_users/add'); ?>" method="post">
<div class="required"> 
   <label for="ldap_user_uid">uid</label>
   <?php echo $html->input('LdapUser/uid', array('id' => 'ldap_user_uid', 'size' => '40', 'value' => $ldap_users['LdapUser']['uid'], )) ?>
   <?php echo $html->tagErrorMsg('LdapUser/uid', 'uid can not be blank.') ?>
</div>
<div class="required"> 
   <label for="ldap_user_cn">cn</label>
   <?php echo $html->input('LdapUser/cn', array('id' => 'ldap_user_cn', 'size' => '40', 'value' => $ldap_users['LdapUser']['cn'], )) ?>
   <?php echo $html->tagErrorMsg('LdapUser/cn', 'cn can not be blank.') ?>
</div>
<div class="required"> 
   <label for="ldap_user_userpassword">userpassword</label>
   <?php echo $html->input('LdapUser/userpassword', array('id' => 'ldap_user_userpassword', 'size' => '40', 'value' => $ldap_users['LdapUser']['userpassword'], )) ?>
   <?php echo $html->tagErrorMsg('LdapUser/userpassword', 'userpassword can not be blank.') ?>
</div>
<div class="required"> 
   <label for="ldap_user_loginshell">loginshell</label>
   <?php echo $html->input('LdapUser/loginshell', array('id' => 'ldap_user_loginshell', 'size' => '40', 'value' => $ldap_users['LdapUser']['loginshell'], )) ?>
   <?php echo $html->tagErrorMsg('LdapUser/loginshell', 'loginshell can not be blank.') ?>
</div>
<div class="required"> 
   <label for="ldap_user_uidnumber">uidnumber</label>
   <?php echo $html->input('LdapUser/uidnumber', array('id' => 'ldap_user_uidnumber', 'size' => '40', 'value' => $newuid )) ?>
   <?php echo $html->tagErrorMsg('LdapUser/uidnumber', 'uidnumber can not be blank.') ?>
</div>
<div class="required"> 
   <label for="ldap_user_gidnumber">gidnumber</label>
   <?php echo $html->input('LdapUser/gidnumber', array('id' => 'ldap_user_gidnumber', 'size' => '40', 'value' => $ldap_users['LdapUser']['gidnumber'], )) ?>
   <?php echo $html->tagErrorMsg('LdapUser/gidnumber', 'gidnumber can not be blank.') ?>
</div>
<div class="required"> 
   <label for="ldap_user_homedirectory">homedirectory</label>
   <?php echo $html->input('LdapUser/homedirectory', array('id' => 'ldap_user_homedirectory', 'size' => '40', 'value' => $ldap_users['LdapUser']['homedirectory'], )) ?>
   <?php echo $html->tagErrorMsg('LdapUser/homedirectory', 'homedirectory can not be blank.') ?>
</div>
<div class="required"> 
   <label for="ldap_user_gecos">gecos</label>
   <?php echo $html->input('LdapUser/gecos', array('id' => 'ldap_user_gecos', 'size' => '40', 'value' => $ldap_users['LdapUser']['gecos'], )) ?>
   <?php echo $html->tagErrorMsg('LdapUser/gecos', 'gecos can not be blank.') ?>
</div>
<div class="submit"><input type="submit" value="Add" /></div>
</form>
<ul>
<li><?php echo $html->link('List LDAP Users', '/ldap_users/index')?></li>
</ul>