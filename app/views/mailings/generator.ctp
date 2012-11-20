<script type='text/javascript'>
	function generator(clientId,clientName){
		var counter=0;
		var elem=document.getElementById('generator_form').elements;
		for (var i = 0; i < elem.length; i++ ) {
			if (elem[i].type == 'radio' && elem[i].checked == true) {
				document.getElementById('clientName_'+counter).value=clientName;	
				document.getElementById('clientId_'+counter).value=clientId;	
			}
			if (elem[i].type == 'radio'){ 
				counter++;
			}
		}
	}
</script>

<div id="diag" style="position: fixed; right: 10px; top: 10px; background-color: #fff"></div>
<div style='margin-bottom:40px;'>

<ul>
<li>Select the radio button of the field you wish to populate.
<li>Enter the client name in the Client Search field.
<li>Click on the link that pops up to populate field you selected.
<li>The order they appear here will be the order they apear in the newsletter.
<li>To populate the form with dummy data, click <a href=?test=1>here</a>
</ul>
<br>
<form method='post' id='generator_form' action='generated' onsubmit='return validateJunkAndStuff();'>
<input type="hidden" name="tid" value="<?= $templateId; ?>">
<? echo $this->renderElement("input_search",array('name' => 'query','label'=>'Client Search','controller' => 'generator')); ?>
<div style='clear:both;'></div>
<br>
<?

$showCaptions = true;
if ($templateId == 'fg1') {
	$showCaptions = false;
	$client_arr = array_slice($client_arr, 0, 12, true);
	echo '<div><b>FG Weekly Email</b></div>';
}

if (!isset($client_arr)){

	for($i=0;$i<=14;$i++){
		$client_arr[$i]='';
	}

}

$i=0;
foreach($client_arr as $clientId=>$name){
	if ($i==0 && $showCaptions)echo "<p><b>New Offers</b></p>";
	else if ($i==4 && $showCaptions) echo "<p><b>Top Deals</b></p>";
	else if ($i==8 && $showCaptions) echo "<p><b>More Top Deals</b></p>";
	else if ($i==12 && $showCaptions) echo "<p><b>Editor's Picks</b></p>";
	echo "<input type='radio' name='index' id='index_$i' value='$i' ";
	if ($i==0)echo "checked";	
	echo ">";
	$val='';
	$id='';
	if (isset($_GET['test'])){
		$val=$name;
		$id=$clientId;	
	}
	echo "<input type='text' name='clientName[]' id='clientName_$i' size='30' value='".$val."' onFocus=\"document.getElementById('index_$i').checked=true\"><br>";
	echo "<input type='hidden' name='clientId_arr[]' id='clientId_$i' value='$id'><br>";
	$i++;
}

?>

Month (mm):<input type='text' name='month' size='2' id='mm_two_digit'>
Day (dd):<input type='text' name='day' size='2' id='dd_two_digit'>
Year (yy):<input type='text' name='year' size='2' id='yy_two_digit'>
<br>
<br>
Version: <input type='text' name='version' size='2'>
<br><br>
<input type='submit' value='submit'>
</form>


</div>

<script>jQuery('.search-input-with-livesearch input[name="query"]').liveSearch({url: "/ajax_search?searchtype=generator"});</script>

<script>

function validateJunkAndStuff(){

	var mm=document.getElementById("mm_two_digit").value;
	var dd=document.getElementById("dd_two_digit").value;
	var yy=document.getElementById("yy_two_digit").value;

	if (mm.length!=2){
		alert("Months must be 2 digits long");
		return false;
	}
	if (dd.length!=2){
		alert("Day of month must be 2 digits long");
		return false;
	}
	if (yy.length!=2){
		alert("Years must be 2 digits long");
		return false;
	}

	return true;


}

</script>

