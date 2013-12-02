<fieldset class="collapsible lastCollapsible">
	<h3 class="handle">Formats</h3>
	<div class="collapsibleContent">
		<div class="controlset2">
		<?php echo $form->input('Format', array('label' => 'Allowed Formats', 'multiple' => 'checkbox')); ?>
		</div>
		<fieldset class='fullBorder' style='clear: both;'>
			<legend>Defaults for selected formats</legend>
		<?php foreach($formats as $formatId => $format): ?>
			<div id='offerTypesDefaults<?=$formatId?>'>
				<? if(@in_array($formatId, $this->data['Format']['Format'])): ?>
					<?=$this->renderElement('../packages/format_defaults_'.$formatId); ?>
				<? endif; ?>
			</div>
			<?=$ajax->observeField('FormatFormat'.$formatId, array(
																'url' => array('action' => 'getOfferTypeDefaultsHtmlFragment', @$this->data['Package']['packageId']),
																'update' => 'offerTypesDefaults'.$formatId,
																'indicator' => 'spinner',
																'before' => 'if(!($("FormatFormat'.$formatId.'").checked)) { fancyHide("offerTypesDefaults'.$formatId.'") }',
																'complete' => 'if($("FormatFormat'.$formatId.'").checked) { fancyShow("offerTypesDefaults'.$formatId.'") }'))?>
		<?php endforeach; ?>
		</fieldset>
	</div>
</fieldset>