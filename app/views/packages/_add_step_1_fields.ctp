<div id='clientInner_<?=$rowId?>'>
<?php if($rowId > 0): ?>
<hr>
<h3><?=$client['Client']['name']?> </h3>
<?php endif; ?>
<?php
		echo $form->input('ClientLoaPackageRel.'.$rowId.'.clientId', array('value' => $clientId, 'type' => 'hidden'));
		echo $form->input('ClientLoaPackageRel.'.$rowId.'.loaId', array('options' => $loaIds));

        if (!isset($additionalClient)) {
        	// TICKET634: package creation - FG/LL choice does not reflect LOA data
        	$cSiteIds = array();
        	foreach ($siteIds as $key=>$s) {
        		if (in_array(strtolower(preg_replace("/[^A-Za-z0-9]/","", $s)),$client['Client']['sites'])) {
        			$cSiteIds[$key] = $s;
        		}	
        	}
          echo $form->hidden('Package.siteId', array('value' => 1));
        }
?>
	<!-- <div class="input text"><label>LOA Expiration Date:</label><div id="loaExpirationDate<?=$rowId?>" style="display: inline"></div></div> -->
    <?php if (!isset($additionalClient)): ?>
        <div class="input text"><label>Package Type</label><? echo $form->select('packageType', array(0 => 'Standard Package', 1 => 'Hotel Offer', 2 => 'Multi-client Package'), 0, null, false); ?></div>
    <?php endif; ?>
	
	<?php if ($rowId == 0 && !$showFirstPercentOfRevenue): ?>
        <div id='firstPercentOfRevenue' style="display: none">
	<?php endif; ?>
<?php
	if ($numClients == 1):
        echo $form->input('ClientLoaPackageRel.'.$rowId.'.percentOfRevenue', array('value' => 100));
	else:
        echo $form->input('ClientLoaPackageRel.'.$rowId.'.percentOfRevenue');
	endif;
?>
	<?php if ($rowId == 0 && !$showFirstPercentOfRevenue): ?>
	</div>
	<?php endif; ?>
    
    <?php if ($rowId > 0) {
        echo '<br />'.$html->link($html->image('delete.png', array('align' => 'top', 'style' => 'padding-right: 5px;')).'Remove', '#', array('onclick' => 'new Effect.Highlight("client_'.$rowId.'"); $("client_'.$rowId.'").fade({afterFinish: function() { $("client_'.$rowId.'").remove() }});'), null, false);
    }?>
    
<script type="text/javascript">
<?php
//echo $ajax->remoteFunction(
//array('url' => array( 'controller' => 'loas', 'action' => 'getExpiration', '\'+$F("ClientLoaPackageRel'.$rowId.'LoaId")+\''),
//		'update' => 'loaExpirationDate'.$rowId
//));
?>
</script>
<?php /*echo $ajax->observeField('ClientLoaPackageRel'.$rowId.'LoaId',
							array('url' => array('controller' => 'loas', 'action' => 'getExpiration'),
								'update' => 'loaExpirationDate'.$rowId,
								'frequency' => 0.2,
								'indicator' => 'spinner')
							);*/
?>
</div>
