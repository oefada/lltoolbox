<div class="packages form">
<?php echo $form->create('Package', array('action' => 'editFormats'));?>
	<fieldset>
 		<legend><?php __('Edit Offer Type Defaults');?></legend>
	<?php
		foreach ($this->data['Format'] as $k => $v) {
			echo '<div class="boxFloat">';
			echo '	<div class="boxFloatHdr">' . $v['formatName'] . '</div>';
			foreach ($v['OfferType'] as $offerType) {
				echo '<div style="margin-left: 20px;">';
				echo '	<div class="boxFloatHdrSmall">' . $offerType['offerTypeName'] . '</div>';
				echo '  <div style="margin-left:15px;">';
				
				foreach ($this->data['PackageOfferTypeDefFieldRel'] as $defFields) {
					if ($defFields['offerTypeId'] == $offerType['offerTypeId']) {
						echo $defFields['OfferTypeDefField']['fieldName'] . '<br />';
						echo $form->input('PackageOfferTypeDefFieldRel.defValue.' . $defFields['packageOfferTypeDefFieldRelId'], array('value' => $defFields['defValue'], 'label' => ''));
					}
				}
				echo '  </div>';
				echo '</div>';
			}
			echo '</div>';
		}
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Packages', true), array('action'=>'index'));?></li>
	</ul>
</div>
