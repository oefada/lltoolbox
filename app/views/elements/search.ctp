<?php
//Check whether to render a search bar or not, depending on whether this controller has a search method
$controller = false;
if(isset($this->viewVars['searchController'])) {
	$controllerName = Inflector::pluralize($this->viewVars['searchController']);
} else {
	$controllerName = $this->params['controller'];
}
$controllerName = Inflector::camelize($controllerName);	//make sure the controller name is always in the right format
$controllerUrl = Inflector::underscore($controllerName);
$fullControllerName = $controllerName.'Controller';

if(!isset($this->viewVars['searchController']) &&class_exists($fullControllerName)) {					//just in case the controller doesn't exist
	$controller = new $fullControllerName;
}

if ((isset($this->viewVars['searchController']) && $this->viewVars['searchController']) 
		|| method_exists($controller , 'search') 
		|| isset($this->searchController)):
?>
<div id='search-bar' class="clearfix">
<div id='search-bar-inner' class="clearfix">
<?  if ($controllerName=='Users'){ ?>

<!--specific search form-->
<form action="/users/search" class="ssform" onsubmit='return validateFields()'>
<div style="float:left;position:relative;top:-3px;"> Specific Search: </div>
<input class="ssfield" id='firstName' type="text" name="firstName" value="first name" onfocus="clearField(this, 'first name');"> 
<input class="ssfield" id='lastName' type="text" name="lastName" value="last name" onfocus="clearField(this, 'last name');"> 
<input class="ssfield" id='username' type="text" name="username" value="username" onfocus="clearField(this,'username');"> 
<input class="ssfield" id='email' type="text" name="query" value="email" onfocus="clearField(this,'email');"> 
<input type="submit" value="Search">
</form>

<script>
function validateFields(){

	// This is done so as to have the url in a form that the pagintor can use when navigating via
	// prev/next or page numbers
	var url='/users/search';
	if (jQuery("#firstName").val()!='first name' && jQuery("#firstName").val()!=''){
		url+='/firstName:'+jQuery("#firstName").val();
	}
	if (jQuery("#lastName").val()!='last name' && jQuery("#lastName").val()!=''){
		url+='/lastName:'+jQuery("#lastName").val();
	}
	if (jQuery("#username").val()!='username' && jQuery("#username").val()!=''){
		url+='/username:'+jQuery("#username").val();
	}
	if (jQuery("#email").val()!='email' && jQuery("#email").val()!=''){
		url+='?query='+jQuery("#email").val();
	}
	window.location=url;
	return false;

}
function clearField(obj, value){
	if (obj.value=value){
		obj.value='';
	}
}
</script>
<style>
.ssform{
	position:absolute;
	top:5px;
	left:410px;
}
.ssfield{
	width:140px !important;
}
</style>

<? } ?>


<?php $defSearchValue = "Search {$controllerName}"; ?>
<form accept-charset="UNKNOWN" enctype="application/x-www-form-urlencoded" method="get" id="search-form" action="/<?= $controllerUrl ?>/search">
	<div class="clearfix">
		<div class="search-input-with-livesearch" on>
			<label>
			<input autocomplete='off' maxlength="2147483647" name="query" type="text" value="<?=$defSearchValue?>" />
			<div id="search-input-with-livesearch" class="auto_complete"><!-- Results will load here --></div>
			</label>
			<input type="submit" value="Search" />
		</div>
	</div>
</form>

<?= $javascript->link('livesearch.js?v=121121'); ?>
<script>jQuery('.search-input-with-livesearch input[name="query"]').liveSearch({id: "search-input-with-livesearch", url: "/ajax_search?searchtype=<?= $controllerUrl ?>"});</script>
</div>
</div>
<?php endif; //end method exists check ?>
