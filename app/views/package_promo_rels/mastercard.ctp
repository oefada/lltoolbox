<? $this->pageTitle = 'MasterCard Promotion Tool' ?>

<script type="text/javascript">
//<![CDATA[
Event.observe(window, 'load', function() {
	Event.observe('PackagePromoRelClientId', 'change', function() {
		new Ajax.Updater('clientNameField', '/clients/getClientNameById/'+$('PackagePromoRelClientId').value, 
		{
			onLoading: function(){Element.show('spinner')},
		 	onComplete: function(){Element.hide('spinner');}
		});
	});
});
//]]>
</script>
<div class="advancedSearch">
	<?php echo $form->create(array('action' => 'mastercard'))?>
  <fieldset>
	  <table width="100%" border="0" cellpadding="0" cellspacing="0" class="noBorder">
	    <tr>
	      <td style="font-weight:bold; color: #900; width: 100px">ADD PACKAGE</td>
	      <td width="100"><label for="PackagePromoRelClientId" style="width: auto"><strong>Client ID:</strong></label></td>
	      <td width="100"><?php echo $form->input('clientId', array('label' => false)); ?></td>
	      <td width="100"><label for="PackagePromoRelPackageId" style="width: auto"><strong>Package ID:</strong></label></td>
	      <td width="100"><?php	echo $form->input('packageId', array('label' => false));	?></td>
	      <td><strong>Client Name:</strong> <span id="clientNameField"><?=@$clientName?></span></td>
        </tr>
	    <tr>
	      <td width="100">&nbsp;</td>
	      <td width="100"><label for="PackagePromoRelBenefitCopy" style="width: auto">Benefit Copy:</label></td>
	      <td colspan="4"><?php echo $form->input('benefitCopy', array('label' => false, 'rows' => 2)); ?></td>
        </tr>
	    <tr>
	      <td width="100">&nbsp;</td>
	      <td width="100">&nbsp;</td>
	      <td><?php echo $form->submit('Add'); ?></td>
	      <td width="100">&nbsp;</td>
	      <td width="100">&nbsp;</td>
	      <td>&nbsp;</td>
        </tr>
      </table>
</fieldset>
	<?php echo $form->end();?>
</div>
<div class="index">
<?php echo $form->create(array('action' => 'mastercard', 'id' => 'ActivateInactivateForm', 'name' => 'ActivateInactivateForm'))?>
<?php echo $form->hidden('activate_inactivate') ?>
<table border="0" style="margin-bottom: 30px; border: 0">
<thead>
  <tr>
    <td colspan="9"><h2>Live Packages</h2></td>
    <th colspan="3" style="background-color: #7f0000; color: #fff" class="rowBorderDark">Totals</th>
  </tr>
  <tr class="rowBorderDark">
    <th><a href="#">Inactivate</a></th>
    <th><a href="#">Client ID</a></th>
    <th><a href="#">Pkg . ID</a></th>
    <th><a href="#">Styles</a></th>
    <th><a href="#">Client Name</a></th>
    <th><a href="#">End Date</a></th>
    <th><a href="#">Ticket #</a></th>
    <th><a href="#">Purchase Price</a></th>
    <th><a href="#">Disc.</a></th>
    <th style="background-color: #7f0000; color: #fff">#Pkg</th>
    <th style="background-color: #7f0000; color: #fff">Disc.</th>
    <th style="background-color: #7f0000; color: #fff">Collected</th>
  </tr>
  </thead>
  <tbody class="rowBorderDark">
  <?php foreach($activePackages as $k => $package):?>
	<?php echo $this->renderElement('package_promo_rels/mastercard_row', array('k' => $k, 'package' => $package, 'checkboxAction' => 'inactivate')) ?>
   <?php endforeach; ?>
   </tbody>
   <tfoot class="rowBorderDark">
  <tr>
    <td colspan="9"><div style="float: left; clear: none;"><a href="#" onclick="javascript:$('ActivateInactivateForm').submit(); return false;">Submit</a></div>
	<div style="float: right; clear: none; font-weight: bold;" class="textRed">TOTAL FOR LIVE PACKAGES</div>
	</td>
    <th>0</th>
    <th>$0</th>
    <th>$0</th>
  </tr>
  </tfoot>
</table>
<div class="collapsible">
<div class="handle">Inactive Packages</div>
<div class="collapsibleContent">
	<table border="0" style="margin-bottom: 30px; border: 0">
	<thead>
	  <tr class="rowBorderDark">
	    <th><a href="#">Inactivate</a></th>
	    <th><a href="#">Client ID</a></th>
	    <th><a href="#">Pkg . ID</a></th>
	    <th><a href="#">Styles</a></th>
	    <th><a href="#">Client Name</a></th>
	    <th><a href="#">End Date</a></th>
	    <th><a href="#">Ticket #</a></th>
	    <th><a href="#">Purchase Price</a></th>
	    <th><a href="#">Disc.</a></th>
	    <th style="background-color: #7f0000; color: #fff">#Pkg</th>
	    <th style="background-color: #7f0000; color: #fff">Disc.</th>
	    <th style="background-color: #7f0000; color: #fff">Collected</th>
	  </tr>
	  </thead>
	  <tbody class="rowBorderDark">
	  <?php foreach($inactivePackages as $k => $package):?>
		<?php echo $this->renderElement('package_promo_rels/mastercard_row', array('k' => $k, 'package' => $package, 'checkboxAction' => 'inactivate')) ?>
	   <?php endforeach; ?>
	   </tbody>
	   <tfoot class="rowBorderDark">
	  <tr>
	    <td colspan="9"><div style="float: left; clear: none;"><a href="#" onclick="javascript:$('ActivateInactivateForm').submit(); return false;">Submit</a></div>
		<div style="float: right; clear: none; font-weight: bold;" class="textRed">TOTAL FOR LIVE PACKAGES</div>
		</td>
	    <th>0</th>
	    <th>$0</th>
	    <th>$0</th>
	  </tr>
	  </tfoot>
	</table>
</div></div>
<?php echo $form->end();?>
</div>

<?php
echo $prototip->renderTooltips();
?>