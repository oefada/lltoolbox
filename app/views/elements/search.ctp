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

if ($this->viewVars['searchController'] || method_exists($controller , 'search') || isset($this->searchController)):
 ?>
<div id='search-bar' class="clearfix">
<div id='search-bar-inner' class="clearfix">
<?php $defSearchValue = "Search {$controllerName}"; ?>
<form accept-charset="UNKNOWN" enctype="application/x-www-form-urlencoded" method="get" action="/<?=$controllerName?>/search">
	<div class="clearfix">
		<div class="search-input-with-livesearch" on>
			<input id="query" maxlength="2147483647" name="query" type="text" value="<?=$defSearchValue?>" onfocus="if($F(this) == '<?=$defSearchValue?>') { $(this).value = '';} else { $('livesearch').show(); }" onblur="Element.hide.delay(0.2, 'livesearch'); if($F(this) == '') { $(this).value = '<?=$defSearchValue?>' }" />
			<div id="livesearch" class="auto_complete"><!-- Results will load here --></div>
		</div>
		<input type="submit" value="Search" />
	</div>
</form>
 
<?php
$options = array(
	'update' => 'livesearch',
	'url'    => "/{$controllerUrl}/search",
	'frequency' => 1,
	'loading' => "if(\$F('query') != '' && \$F('query') != '$defSearchValue') { Element.show('spinner') }",
	'complete' => "if(\$F('query') != '' && \$F('query') != '$defSearchValue') { Element.hide('spinner');Effect.Appear('livesearch') }"
);

print $ajax->observeField('query', $options);
?>

</div>
</div>
<?php endif; //end method exists check ?>
