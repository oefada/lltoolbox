<fieldset style='border: 1px solid #e5e5e5' class='smallLabels'>
<legend>Auction</legend>
<div style="float: left; clear: none">
<strong><em>Standard Auction</em></strong>
<?=$form->input('PackageOfferTypeDefField.1.offerTypeId', array('value' => 1, 'type' => 'hidden')) ?>
<?=$form->input('PackageOfferTypeDefField.1.openingBid', array('label' => 'Opening Bid', 'size' => 5)) ?>
<?=$form->input('PackageOfferTypeDefField.1.percentRetail', array('label' => '% of Retail', 'size' => 5, 'disabled' => 'disabled')) ?>
<?=$form->input('PackageOfferTypeDefField.1.numWinners', array('value' => 1, 'label' => 'Number of Winners', 'size' => 5, 'readonly' => 'readonly')) ?>
</div>
<div style="float: left; clear: none">
<strong><em>Best Shot</em></strong>
<?=$form->input('PackageOfferTypeDefField.2.offerTypeId', array('value' => 2, 'type' => 'hidden')) ?>
<?=$form->input('PackageOfferTypeDefField.2.openingBid', array('label' => 'Opening Bid', 'size' => 5)) ?>
<?=$form->input('PackageOfferTypeDefField.2.percentRetail', array('label' => '% of Retail', 'size' => 5, 'disabled' => 'disabled')) ?>
<?=$form->input('PackageOfferTypeDefField.2.numWinners', array('label' => 'Number of Winners', 'size' => 5)) ?>
</div>
<div style="float: left; clear: none">
<strong><em>Dutch</em></strong>
<?=$form->input('PackageOfferTypeDefField.6.offerTypeId', array('value' => 6, 'type' => 'hidden')) ?>
<?=$form->input('PackageOfferTypeDefField.6.openingBid', array('label' => 'Opening Bid', 'size' => 5)) ?>
<?=$form->input('PackageOfferTypeDefField.6.percentRetail', array('label' => '% of Retail', 'size' => 5, 'disabled' => 'disabled')) ?>
<?=$form->input('PackageOfferTypeDefField.6.numWinners', array('label' => 'Number of Winners', 'size' => 5)) ?>
</div>
</fieldset>