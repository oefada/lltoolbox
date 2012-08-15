<?

if (isset($msg))echo "<p>$msg</p>";
echo "<h2>Export Silverpop Email Lists and Import to LLTG</h2>";
echo "<p><b>Note:</b> only export 'Email' and 'Opted Out Date'. (In silverpop, that looks like <a href='javascript:void(0);' id='export_img_link'>this</a>.)</p>";

echo "<p><b>Export undeliverables into a csv file and upload here to set optin status of emails to 0 for all newsletters.</b> Note: while you may have a newsletter associated with an undeliverables list, the emails on the list are not specific to that newsletter and apply to all newsletters.</p>";
echo $form->create('sync_emails',array('id'=>'uploadForm','action'=>'index','type'=>'file'));
echo $form->file('csv');
echo $form->button('Reset',array('type'=>'reset')); 
echo $form->hidden("id", array("value"=>"undeliverables"));
echo $form->end(array('name'=>'Upload Undeliverable Emails','label'=>'Upload Undeliverable Emails'));

echo "<hr>";

echo "<p><b>Export optouts for a specific newsletter and upload here.</b></p>";
echo $form->create('sync_emails',array('id'=>'uploadForm2','action'=>'index','type'=>'file'));
echo $form->file('csv');
echo $form->hidden("id", array("value"=>"optopts"));
echo $form->button('Reset',array('type'=>'reset')); 
echo $form->input('mailingList', array('type'=>'select','label'=>false, 'options'=>$nlIdArr));
echo $form->end(array('name'=>'Upload Optout Emails','label'=>'Upload Optout Emails'));

?>

<script>

jQuery(document).ready(function() {

	jQuery("#export_img_link, #export_img_link2").click(function(){
		jQuery("#export_img").toggle();
	});

	jQuery("#uploadForm2").submit(function(){
		if (jQuery("#sync_emailsMailingList option:selected").val()==0){
			alert("Please select the newsletter that these opt outs belong to.");
			return false;
		}
		return true;
	});

});
</script>
<br><br>
<img border=1 src='/img/export.jpg' id='export_img' style='display:none;border:1px solid black;'>
