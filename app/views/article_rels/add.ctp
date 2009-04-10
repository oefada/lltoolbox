<div class="articleRels form">
<?php echo $form->create('ArticleRel', array('url' => "add/$articleRelTypeId/$articleId"));?>
	<fieldset>
 		<legend><?php __('Add ArticleRel');?></legend>
	<?php
		echo $form->input('articleId', array('type' => 'hidden'));
		echo $form->input('articleRelTypeId');
		echo $form->input('refId');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
