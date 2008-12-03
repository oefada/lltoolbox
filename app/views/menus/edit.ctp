<div class="menus form">
<?php echo $form->create('Menu');?>
	<fieldset>
 		<legend><?php __('Edit Menu');?></legend>
	<?php
		echo $form->hidden('menuId');
		echo $form->input('menuTitleImageId');
	?>
	<div id='ajax_indicator' style="display: none;"><img src='/img/ajax-loader.gif' alt='Loading...' /></div>
	<div id="menuTitleImagePreview"></div>
	<?
		echo $form->input('menuName');
		echo $form->input('menuSubtitle');
		echo $form->input('Style');
	?>
	</fieldset>
	
<?php
echo $form->end('Submit');
echo '<div class="delete">';
echo $html->link('Delete Menu', array('action' => 'delete', $this->data['Menu']['menuId']), array(), 'Are you sure you wish to delete this menu? If this menu is shared by more than one style, the delete will propogate!');
echo '</div>';
?>
<br /><br />
</div>
<?= $ajax->observeField('MenuMenuTitleImageId',
                                   array(  'url'=>'fetch_title_image',
                                   'update'=>'menuTitleImagePreview',
                                  'loading'=>"Element.show('menuTitleImagePreview');           
                                  Element.show('ajax_indicator')",
                                  'complete'=>"Element.hide('ajax_indicator');
                                 Effect.Appear('menuTitleImageDiv')",
                                 'onChange'=>true));
?>