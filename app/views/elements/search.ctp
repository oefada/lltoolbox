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

if (@$this->viewVars['searchController'] || method_exists($controller , 'search') || isset($this->searchController)):
?>
<div id='search-bar' class="clearfix">
<div id='search-bar-inner' class="clearfix">
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
<?= $javascript->link('livesearch'); ?>
<script>jQuery('.search-input-with-livesearch input[name="query"]').liveSearch({id: "search-input-with-livesearch", url: "/ajax_search?searchtype=<?= $controllerUrl ?>"});</script>
</div>
</div>
<?php endif; //end method exists check ?>
