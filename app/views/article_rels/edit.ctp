<div class="articleRels form">
<?php echo $form->create('ArticleRel', array('url' => "edit/$relId/$articleId"));?>
	<fieldset>
 		<legend><?php __('Edit ArticleRel');?></legend>
	<?php
		echo $form->input('articleRelId');
		echo $form->input('articleId', array('type' => 'hidden'));
		echo $form->input('articleRelTypeId');
		echo $form->input('refId');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
