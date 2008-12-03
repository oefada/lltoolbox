<?php

if (isset($result)) {
print '<ul>';
foreach ($result as $style) 
{
	$display = $style['Style']['styleId'] . ' ' . $style['Style']['styleName'];
	echo '<li>';
		echo $html->link($display, array('controller' => 'menus', 'action' => 'edit_by_style', 'id' => $style['Style']['styleId']));
	echo '</li>';
}
print '</ul>';
}
?>