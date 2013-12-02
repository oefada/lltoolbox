<div class="menuItems form">
<?php echo $ajax->form('edit', 'post', array('url' => "/menus/{$this->data['MenuItem']['menuId']}/menu_items/edit/{$this->data['MenuItem']['menuItemId']}", 'update' => 'MB_content', 'model' => 'MenuItem', 'complete' => 'closeModalbox()'));?>
	<fieldset style="width: 400px;">
 		<legend><?php __('Edit MenuItem');?></legend>
	<?php
		echo $form->hidden('menuItemId');
		echo $form->hidden('menuId');
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
	</fieldset>
<?php echo $form->end('Submit');?>
</div>

<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>