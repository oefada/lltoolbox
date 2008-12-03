<h2>Editing Menus for <?= $menus['Style']['styleName'] ?></h2>
<span class="deemphasize">Drag and drop the menus or menu items to re-order</span>
<div id='ajax_indicator' style="display: none;"><img src='/img/ajax-loader.gif' alt='Loading...' /></div>
<div id='menus'>
<?php foreach($menus['Menu'] as $mk=>$menu): ?>
	<fieldset id='menu_<?=$menu['menuId']?>'><legend class='sortableElement' style="font-size: 15px;">&nbsp;<?php echo $html->image('drag_handle.gif', array('style' => 'cursor: move;'))?> <?= $menu['menuId'] ?> - <?= $menu['menuName'] ?> (<?php echo $html->link(__('Edit Menu', true), array('controller'=> 'menus', 'action'=>'edit', 'id' => $menu['menuId'])); ?>)</legend>
<?php 
	if ( $menu['MenuTitleImage']['headerImageUrl'] ):
?>
<div class="deemphasize">header preview</div>
	<div style="margin-bottom: 5px;"><?= $html->image($menu['MenuTitleImage']['headerImageUrl'], array('alt' => $menu['MenuTitleImage']['menuTitleImageName']))?></div>
<?php
endif;
?>
<?php
if (isset($menu['menuSubtitle']) && $menu['menuSubtitle'] != ''):
	echo '<div class="deemphasize">subtitle</div><p>'.$menu['menuSubtitle'].'</p>';
endif;
?>

<?= $this->renderElement('/menu_items/sortable_menu_items', array('menu' => $menu))?>

</fieldset>
<?php endforeach; ?>
</div>

<?php if(!$menus['Menu']): ?> 
	<div class="notice">No menus for this style yet,
	<?php
	echo $html->link(__('add a new menu.', true), array('controller'=> 'menus', 'action'=>'add', 'styleId' => $menus['Style']['styleId'])); ?>
	</div>
<?php endif; ?>

<?php
if(isset($menu['menuId'])) {
echo $ajax->sortable('menus',
						array(
							'tag' => 'fieldset',
							'url' => array('controller' => 'menus', 'action' => 'order', 'menuId' => $menu['menuId']),
							'scroll' => 'window'
							)
					);
}
?>