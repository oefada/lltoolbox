<?php $defSearchValue = "Search {$this->params['controller']}"; ?>
<form accept-charset="UNKNOWN" enctype="application/x-www-form-urlencoded" method="get" action="/<?=$this->params['controller']?>/search">
<input id="query" maxlength="2147483647" name="query" size="20" type="text" value="<?=$defSearchValue?>" onfocus="if($F(this) == '<?=$defSearchValue?>') { $(this).value = '';}" onblur="if($F(this) == '') { $(this).value = '<?=$defSearchValue?>' }"/>
<div id="loading" style="display: none; "><?php echo $html->image("spinner.gif") ?></div>
</form>
 
<?php
$options = array(
	'update' => 'livesearch',
	'url'    => "/{$this->params['controller']}/search",
	'frequency' => 1,
	'loading' => "if(\$F('query') != '' && \$F('query') != '$defSearchValue') { Element.hide('livesearch');Element.show('loading') }",
	'complete' => "if(\$F('query') != '' && \$F('query') != '$defSearchValue') { Element.hide('loading');Effect.Appear('livesearch') }"
);

print $ajax->observeField('query', $options);
?>
<div id="livesearch" class="auto_complete"><!-- Results will load here --></div>