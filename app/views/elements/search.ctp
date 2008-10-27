<?php
//Check whether to render a search bar or not, depending on whether this controller has a search method
$controller = false;
if(isset($this->searchController)) {
	$controllerName = $this->searchController;
} else {
	$controllerName = ($this->params['controller']);
	$controllerName = Inflector::camelize($controllerName);	//make sure the controller name is always in the right format
}

$fullControllerName = $controllerName.'Controller';			
if(class_exists($fullControllerName)) {					//just in case the controller doesn't exist
	$controller = new $fullControllerName;
}

if (is_a($controller, $fullControllerName) && ( method_exists($controller , 'search') || isset($this->searchController) )):
 ?>
<div style="float: left;" class="clearfix">
<div style="clear: both;">
<?php $defSearchValue = "Search {$controllerName}"; ?>
<form accept-charset="UNKNOWN" enctype="application/x-www-form-urlencoded" method="get" action="/<?=$controllerName?>/search">
<input id="query" maxlength="2147483647" name="query" type="text" value="<?=$defSearchValue?>" onfocus="if($F(this) == '<?=$defSearchValue?>') { $(this).value = '';}" onblur="if($F(this) == '') { $(this).value = '<?=$defSearchValue?>' }"/>
<input type="submit" value="Search" />
</form>
 
<?php
$options = array(
	'update' => 'livesearch',
	'url'    => "/{$controllerName}/search",
	'frequency' => 1,
	'loading' => "if(\$F('query') != '' && \$F('query') != '$defSearchValue') { Element.hide('livesearch');Element.show('spinner') }",
	'complete' => "if(\$F('query') != '' && \$F('query') != '$defSearchValue') { Element.hide('spinner');Effect.Appear('livesearch') }"
);

print $ajax->observeField('query', $options);
?>
</div>
<div id="livesearch" class="auto_complete"><!-- Results will load here --></div>
</div>
<?php endif; //end method exists check ?>
