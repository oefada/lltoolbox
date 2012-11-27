 <style type="text/css">

	label {
		display: block;
		font-weight: bold;
	}
	
	label em {
		font-size: 10px;
		text-transform: uppercase;
		color: #999;
		font-style: normal;
		font-weight: normal;
	}
	
	.cmsSaved {
		color: red;
		padding: 10px;
		border: 1px solid red;
		margin: 0 0 20px 0;
		width: 120px;
	}
	
	.title a {
		font-size: 13px;
		font-weight: normal;
	}
	
	.cmsBox {
		width: 90%;
		height: 300px;
	}
	
</style>



 
<?php $this->pageTitle = 'CMS Edit: ' . $cmsId . ' <a href="/cms">Return to CMS Tools</a>'; ?>
<?php $this->set('hideSidebar', true); ?>

<?php
	if(isset($cmsSaved)){
		echo "<div class=\"cmsSaved\">Data Saved</div>";
	}
?>

<?php echo $form->create('Cms', array('type' => 'post', 'url' => '/cms/edit/' . $cmsId));?>
<?php echo $form->input('site_id', array('label' => 'Site ID <em>(locked)</em> ', 'disabled' => true, 'value' => $this->data[0]['cms']['site_id'])); ?>
<?php echo $form->input('key', array('label' => 'Key <em>(locked)</em> ', 'disabled' => true, 'value' => $this->data[0]['cms']['key'])); ?>
<?php echo $form->input('description', array('label' => 'Description ', 'value' => $this->data[0]['cms']['description'])); ?>
<?php echo $form->input('html_content', array('label' => 'Code ', 'value' => $this->data[0]['cms']['html_content'], 'class' => 'cmsBox')); ?>
<?php echo $form->submit('Save Changes'); ?>
<?php echo $form->end(); ?>

