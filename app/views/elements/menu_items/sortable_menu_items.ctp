<ul id='menuItems_<?=$menu['menuId']?>' class="listItemContainer" style="margin-top: 10px;">
<?php foreach($menu['MenuItem'] as $menuItem): ?>		
	<div id="menuItem_<?=$menuItem['menuItemId']?>" class="listItem sortableElement">
		<div id='menuItemActions_<?=$menuItem['menuItemId'] ?>' class="row_actions" >
			<?= $ajax->link($html->image('trash.gif'), 
							array('controller' => 'menu_items', 'action' => 'delete', $menuItem['menuItemId']),
							array(
								'loading' => "Element.show('ajax_indicator')",
								'complete' => "Element.hide('ajax_indicator');
												new Effect.Highlight($('menuItem_{$menuItem['menuItemId']}'), {queue: 'end', duration: 0.5, startcolor: '#ff0000'});
												Effect.Fade($('menuItem_{$menuItem['menuItemId']}'), {queue: 'end', duration: 0.5});"
								),
							'Are you sure you want to delete this link? If the menu is shared by more than one style, the delete will propagate!',
							FALSE)
			?>
			<?php
			echo $html->link('Edit',
					array('controller' => 'menus/'.$menu['menuId'].'/menu_items', 'action' => 'edit', $menuItem['menuItemId']),
					array(
						'title' => 'Edit Menu Item',
						'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
						'complete' => 'closeModalbox()'
						)
					);
			?>
			<?php echo $html->image('drag_handle.gif', array('style' => 'cursor: move;'))?>
		</div>
		<? /* The actual Name is here */ ?>
		<div class="text"><strong><?=$menuItem['menuItemName'] ?></strong> &raquo;
		<?php echo ($menuItem['externalLink']) ? '<span class="deemphasize">url:</span> '.$menuItem['linkTo'] : '<span class="deemphasize">style:</span> '.$menuItem['linkTo'] ?></div>
	</div>				
	<?= $ajax->sortable('menuItems_'.$menu['menuId'],
							array(
								'tag' => 'div',
								'url' => array('controller' => 'menu_items', 'action' => 'order', 'menuId' => $menu['menuId']),
								'scroll' => 'window'
								)
						) ?>
<?php endforeach; ?>
</ul>

<?php
if ( sizeof($menu['MenuItem']) == 0 ) {
	echo '<div class="notice">There are no menu items for this menu. ';
	$addText = 'Add one now.';
} else {
	$addText = 'Add another menu item';
}

echo $html->link($addText,
				array('controller' => 'menus/'.$menu['menuId'].'/menu_items', 'action' => 'add'),
				array(
					'title' => 'Add Menu Item',
					'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
					'complete' => 'closeModalbox()'
					),
				null,
				false
				);
?>
<?php if ( sizeof($menu['MenuItem']) == 0 ) { echo "</div>"; } ?>