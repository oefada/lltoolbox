<div class="menus form">
<?php echo $form->create('Menu');?>
	<fieldset>
 		<legend><?php __('Add Menu');?></legend>
	<?php
		echo $form->input('menuTitleImageId');
	?>
	<div id='ajax_indicator' style="display: none;"><?php echo $html->image('ajax-loader.gif') ?></div>
	<div id="menuTitleImagePreview"></div>
	<?
		echo $form->input('menuName');
		echo $form->input('menuSubtitle');
		echo $form->input('Style');
	?>
	</fieldset>
	
<?php echo $form->end('Submit');?>
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