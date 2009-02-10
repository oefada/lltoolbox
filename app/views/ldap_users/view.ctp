<h1>View LDAP User</h1>
<table>
	
<?
$i = 0;
foreach($ldap_user['LdapUser'] as $k => $v):
$class = '';
if ($i++ % 2 == 0) {
	$class = ' class="altrow"';
}
?>
<tr<?=$class?>>
   <td><?php echo Inflector::humanize($k)?></td>
   <td><?php echo is_array($v) ? implode("<br />", $v) : $v; ?></td>
</tr>
<? endforeach; ?>
</table>
<ul>
   <li><?php echo $html->link('List LdapUser',   '/ldap_users/index') ?> </li>
</ul>