<?php
$this->pageTitle = 'Keywords';
?>
<?=$layout->blockStart('toolbar');?>
	<?php echo $html->link(__('<span><b class="icon"></b>Add New Keyword</span>', true), array('action'=>'add'), array('class' => 'button add'), false, false); ?>
<?=$layout->blockEnd();?>


<div class="searchRedirects form">
<?php echo $form->create('SearchRedirect');?>
	<fieldset>
 		<legend><?php __('Add SearchRedirect');?></legend>
		<div class="controlset4">
		<?
		echo $multisite->checkbox('SearchRedirect');
		?>
		</div>
	<?php
		echo $form->input('keyword');
		echo $form->input('redirectUrl');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>