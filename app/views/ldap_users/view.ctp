<h1>View LDAP User</h1>
<table>
	
<? foreach($ldap_user['LdapUser'] as $k => $v): ?>
<tr>
   <td><?php echo Inflector::humanize($k)?></td>
   <td><?php echo $v?></td>
</tr>
<? endforeach; ?>
</table>
<ul>
   <li><?php echo $html->link('List LdapUser',   '/ldap_users/index') ?> </li>
</ul>