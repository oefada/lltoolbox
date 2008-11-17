<fieldset style='border: 1px solid #e5e5e5' class='smallLabels'>
<legend>Auction</legend>
<div style="float: left; clear: none">
<strong><em>Standard Auction</em></strong>
<?=$form->input('PackageOfferTypeDefField.1.offerTypeId', array('value' => 1, 'type' => 'hidden')) ?>
<?=$form->input('PackageOfferTypeDefField.1.default1', array('label' => 'Opening Bid', 'size' => 5)) ?>
<?=$form->input('PackageOfferTypeDefField.1.default2', array('label' => 'Number of Winners', 'size' => 5)) ?>
</div>
<div style="float: left; clear: none">
<strong><em>Best Shot</em></strong>
<?=$form->input('PackageOfferTypeDefField.2.offerTypeId', array('value' => 2, 'type' => 'hidden')) ?>
<?=$form->input('PackageOfferTypeDefField.2.default1', array('label' => 'Opening Bid', 'size' => 5)) ?>
<?=$form->input('PackageOfferTypeDefField.2.default2', array('label' => 'Number of Winners', 'size' => 5)) ?>
</div>
<div style="float: left; clear: none">
<strong><em>Dutch</em></strong>
<?=$form->input('PackageOfferTypeDefField.6.offerTypeId', array('value' => 6, 'type' => 'hidden')) ?>
<?=$form->input('PackageOfferTypeDefField.6.default1', array('label' => 'Opening Bid', 'size' => 5)) ?>
<?=$form->input('PackageOfferTypeDefField.6.default2', array('label' => 'Number of Winners', 'size' => 5)) ?>
</div>
</fieldset>