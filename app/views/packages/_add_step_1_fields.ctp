<div id='clientInner_<?=$rowId?>'>
<hr>
<h5><?=$client['Client']['name']?> <?php if ($rowId > 0) {
	echo $html->link($html->image('delete.png', array('align' => 'top', 'style' => 'padding-right: 5px;')).'Remove', '#', array('onclick' => 'new Effect.Highlight("client_'.$rowId.'"); $("client_'.$rowId.'").fade({afterFinish: function() { $("client_'.$rowId.'").remove() }});'), null, false);
}?></h5>
<?php
		echo $form->input('ClientLoaPackageRel.'.$rowId.'.clientId', array('value' => $clientId));
		echo $form->input('ClientLoaPackageRel.'.$rowId.'.loaId', array('options' => $loaIds));
?>
	<div class="input text"><label>LOA Expiration Date:</label><div id="loaExpirationDate<?=$rowId?>" style="display: inline"></div></div>
	
	<?php if ($rowId == 0 && !$showFirstPercentOfRevenue): ?>
	<div id='firstPercentOfRevenue' style="display: none">
	<?php endif; ?>
<?php
	echo $form->input('ClientLoaPackageRel.'.$rowId.'.percentOfRevenue');
?>
	<?php if ($rowId == 0): ?>
	</div>
	<?php endif; ?>
<script type="text/javascript">
<?php
echo $ajax->remoteFunction(
array('url' => array( 'controller' => 'loas', 'action' => 'getExpiration', '\'+$F("ClientLoaPackageRel'.$rowId.'LoaId")+\''),
		'update' => 'loaExpirationDate'.$rowId
));
?>
</script>
</div>
<? if(!isset($hideAddLink) || $hideAddLink != true): ?>
<div id='addLink_<?=$rowId?>'>
<?=$html->link($html->image('i-create.gif', array('align' => 'top', 'style' => 'padding-right: 5px;')).'Multi product Offers? Add more Clients',
		    	"/packages/selectAdditionalClient/rowId:".$rowId,
				array(
					'title' => 'Select additional client',
					'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
					'complete' => 'closeModalbox()'),
				null,false) ?>
</div>
<? endif; ?>
<?php echo $ajax->observeField('ClientLoaPackageRel'.$rowId.'LoaId',
							array('url' => array('controller' => 'loas', 'action' => 'getExpiration'),
								'update' => 'loaExpirationDate'.$rowId,
								'frequency' => 0.2,
								'indicator' => 'spinner')
							);
?>