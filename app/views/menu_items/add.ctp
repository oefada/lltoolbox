<div class="menuItems form">
<?php echo $ajax->form('add', 'post', array('url' => "/menus/{$this->data['MenuItem']['menuId']}/menu_items/add", 'update' => 'MB_content', 'model' => 'MenuItem', 	'complete' => 'closeModalbox()'));?>
	<fieldset style="width: 400px;">
 		<legend><?php __('Add MenuItem');?></legend>
	<?php
		echo $form->input('menuItemName');
	?>
	
	<div class="input select">
	<label for='MenuItemStyles'>Link To</label>
	<div id="link_to">
		<?php
		if($this->data['MenuItem']['externalLink']) {
			echo $this->render(null, false, 'url_input_form' );
		} else {
			echo $this->render(null, false, 'landing_pages_select_form' );
		}
		?>
	</div>
	</div>
	<?php echo $form->end('Submit');?>
	</fieldset>
</div>

<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>