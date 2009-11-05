<div class="articles form">
<?php echo $form->create('Article');?>
	<fieldset>
 		<legend><?php __('Add Article');?></legend>
		<div class="controlset4">
		<?
		echo $form->input('sites', array('multiple' => 'checkbox'));
		?>
		</div>
	<?php
		echo $form->input('primaryStyleId');
		echo $form->input('articleTitle');
		echo $form->input('articleAuthor');
		echo $form->input('articlePageCount');
		echo $form->input('articleMetaDescription');
		echo $form->input('articleBody', array('style' => 'width:100%;height:700px;'));
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Articles', true), array('action'=>'index')); ?></li>
	</ul>
</div>
