<h1>List LDAP Users</h1>
<style>
 .disabled { color: #ccc;}
</style>
<table>
<tr>
   <th>username</th>
   <th>email</th>
   <th>cn</th>
	<th>groups</th>
   <th>Actions</th>
</tr>
</tr>
<?php foreach ($ldap_users as $key => $value):
$classes = array();
if ($key % 2 == 0) {
	$classes[] = 'altrow';
}
if ( @$value['LdapUser']['useraccountcontrol'] == 66050 ) {
	$classes[] = 'disabled';
}

$class = implode(' ', $classes);
?>
<tr class="<?=$class?>">
   <td><?=@$value['LdapUser']['samaccountname'].'-'.$value['LdapUser']['useraccountcontrol']?></td>
   <td><?=@$value['LdapUser']['mail']?></td>
   <td><?=@$value['LdapUser']['cn']?></td>
	<td>
		<?
		
		if (!empty($value['LdapUser']['memberof']) && is_array($value['LdapUser']['memberof'])) {
			$groups = array();
		foreach ($value['LdapUser']['memberof'] as $v) {
			$cn = strtok($v, ",");
			$cn = substr($cn, 3);
			$groups[] = $cn;
		}
		echo implode(', ', $groups);
		}
		?>
		
	</td>
   <td>
      <?php	echo $html->link('View', '/ldap_users/view/' . $value['LdapUser']['samaccountname'])?>
	<?php if(in_array('Geeks', $userDetails['groups'])): ?>
	  <?php	echo $html->link('Masquerade', '/sessions/masquerade/' . $value['LdapUser']['samaccountname'])?>
	<?php endif; ?>
   </td>
</tr>
<?php endforeach; ?>
</table>
<ul>
   <li><?php echo $html->link('New Ldap User', '/ldap_users/add'); ?></li>
</ul>