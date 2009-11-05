<div class="articleRels form">
<?php echo $form->create('ArticleRel', array('url' => "add/$articleRelTypeId/$articleId"));?>
	<fieldset>
 		<legend><?php __('Add ArticleRel');?></legend>
		<div class="controlset4">
		<?
		echo $form->input('sites', array('multiple' => 'checkbox'));
		?>
		</div>
	<?php
		echo $form->input('articleId', array('type' => 'hidden'));
		echo $form->input('articleRelTypeId');
		echo $form->input('refId');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
