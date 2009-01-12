<fieldset style='border: 1px solid #e5e5e5' class='smallLabels'>
<legend>Fixed Price</legend>
<div style="float: left; clear: none">
<strong><em>Best Buy</em></strong>
<?=$form->input('PackageOfferTypeDefField.4.offerTypeId', array('value' => 4, 'type' => 'hidden')) ?>
<?=$form->input('PackageOfferTypeDefField.4.requestPrice', array('label' => 'Request Price', 'size' => 5)) ?>
<?=$form->input('PackageOfferTypeDefField.4.percentRetail', array('label' => '% of Retail', 'size' => 5, 'disabled' => 'disabled')) ?>
</div>
<div style="float: left; clear: none">
<strong><em>Exclusive</em></strong>
<?=$form->input('PackageOfferTypeDefField.3.offerTypeId', array('value' => 3, 'type' => 'hidden')) ?>
<?=$form->input('PackageOfferTypeDefField.3.requestPrice', array('label' => 'Request Price', 'size' => 5)) ?>
<?=$form->input('PackageOfferTypeDefField.3.percentRetail', array('label' => '% of Retail', 'size' => 5, 'disabled' => 'disabled')) ?>
</div>
</fieldset>