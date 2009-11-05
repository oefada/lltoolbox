<?php
$this->pageTitle = 'Keywords';
?>
<?=$layout->blockStart('toolbar');?>
	<?php echo $html->link(__('<span><b class="icon"></b>Add New Keyword</span>', true), array('action'=>'add'), array('class' => 'button add'), false, false); ?>
	<?php echo $html->link(__('<span><b class="icon"></b>Delete Keyword</span>', true), array('action'=>'add'), array('class' => 'button del'), "Are you sure you want to delete this keyword?", false); ?>
<?=$layout->blockEnd();?>


<div class="searchRedirects form">
<?php echo $form->create('SearchRedirect');?>
	<fieldset>
 		<legend><?php __('Edit SearchRedirect');?></legend>
		<div class="controlset4">
		<?
		echo $form->input('sites');
		?>
		</div>
	<?php
		echo $form->input('searchRedirectId');
		echo $form->input('keyword');
		echo $form->input('redirectUrl');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>