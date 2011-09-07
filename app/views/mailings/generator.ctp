<script src='/js/autosuggest.js'></script>

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

<div style='margin-bottom:40px;'>

<ul>
<li>Select the radio button of the field you wish to populate.
<li>Enter the client name in the Client Search field.
<li>Click on the link that pops up to populate field you selected.
<li>The order they appear here will be the order they apear in the newsletter.
</ul>
<br>

<form method='post' id='generator_form' action='generated'>

	<div on="" class="search-input-with-livesearch">
		<label>
			Client Search:<input type="text" value="" name="query" maxlength="2147483647" autocomplete="off">
			<div class="auto_complete" id="search-input-with-livesearch"><!-- Results will load here --></div>
		</label>
		</div>
<div style='clear:both;'></div>
<br>
<?

// test data. to have it populate the fiels, append ?test to url
$client_arr=array(
31=>'Orient-Express, Botswana',
70=>'Charleston Place',
202=>'Round Hill Hotel and Villas',
79=>'Cobblers Cove',
84=>'CuisinArt Resort & Spa',
96=>'Grand Hotel Huis ter Duin',
108=>'Half Moon, a RockResort',
111=>'Horned Dorset Primavera',
118=>'Hotel Punta Islita',
121=>'Hotelito Desconocido Sanctuary Reserve & Spa',
141=>'La Puertecita Boutique Hotel',
155=>'Lismacue House',
178=>'Mount Nelson Hotel',
193=>'Point Grace',
197=>'Rancho Valencia'
);


$i=0;
foreach($client_arr as $clientId=>$name){
//for($i=0;$i<15;$i++){
	if ($i==0)echo "<p><b>New Offers</b></p>";
	else if ($i==4) echo "<p><b>Top Deals</b></p>";
	else if ($i==8) echo "<p><b>More Top Deals</b></p>";
	else if ($i==12) echo "<p><b>Editor's Picks</b></p>";
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

Month (mm):<input type='text' name='month' size=3>
Day (dd):<input type='text' name='day' size=3>
Year (yyyy):<input type='text' name='year' size=3>
<br>

<input type='submit' value='submit'>
</form>


</div>

<script>jQuery('.search-input-with-livesearch input[name="query"]').liveSearch({url: "/ajax_search?searchtype=generator"});</script>
