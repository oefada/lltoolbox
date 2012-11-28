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
	
	.cmsButton {
	    display: inline;
	    font-size: 110%;
	    font-family: "Tahoma";
	    text-transform: uppercase;
	    padding: 5px 10px;
	    width: auto;
	    vertical-align: bottom;
	    color: #fff;
	    background: #7f0000;
	    margin: 0 0 0 134px;
	}
	
</style>
<script type="text/javascript">
	
	// submit function to send a new client note to clientNotes/add
	previewCms = function(){
		var $=jQuery;

		var v_val = $("#CmsHtmlContent").val();
		
		var mapForm = document.createElement("form");
	    mapForm.target = "Map";
	    mapForm.method = "POST"; // or "post" if appropriate
	    mapForm.action = "<?php echo $cmsEnv; ?>/?hptabstest=1";

	    var mapInput = document.createElement("input");
	    mapInput.type = "text";
	    mapInput.name = "cmsData";
	    mapInput.value = v_val;
	    mapInput.id = "cmsData";
	    mapForm.appendChild(mapInput);
	

	    document.body.appendChild(mapForm);
	    map = window.open("", "Map", "status=0,title=0,height=600,width=1000,scrollbars=1");
		if (map) {
		    mapForm.submit();
		} else {
		    alert('You must allow popups for this map to work.');
		}
	};
	
	
</script>



 
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
<?php echo $form->button('Preview Changes', array('class' => 'cmsButton', 'onclick' => 'previewCms()')); ?>
<?php echo $form->submit('Save Changes'); ?>
<?php echo $form->end(); ?>

