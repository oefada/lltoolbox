<h1>Edit LDAP User</h1>
<?php echo $form->create('LdapUser', array('url' => "/ldap_users/edit/".$ldap_user['LdapUser']['samaccountname']));?>

<div class="required"> 
   <?php echo '';//echo $form->input('password', array('id' => 'ldap_user_userpassword', 'size' => '40', 'value' => $ldap_user['LdapUser']['userpassword'], )) ?>
</div>

<?php echo '';//echo $form->hidden('samaccountname', array('value' => $ldap_user['LdapUser']['samaccountname']))?>

<?php echo $form->hidden('dn', array('value' => $ldap_user['LdapUser']['dn']))?>
<div class="submit"><input type="submit" value="Save" /></div>
</form>
<ul>
   <li><?php echo $html->link('List ldap_user', '/ldap_users/index')?></li>
</ul>