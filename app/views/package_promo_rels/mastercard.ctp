<? $this->pageTitle = 'MasterCard Promotion Tool';
function sortLink($column, $viewVars) {
	$sortBy = $viewVars['sortBy'];
	$sortDirection = $viewVars['sortDirection'];
	
	if ($sortBy == $column) {
		$sortDirection = ($sortDirection == 'ASC') ? 'DESC' : "ASC";
	} else {
		$sortDirection = "ASC";
	}
	return "/package_promo_rels/mastercard/sortBy:$column/sortDirection:$sortDirection";
}
?>

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
	      <td width="100"><label for="PackagePromoRel Copy" style="width: auto">Benefit Copy:</label></td>
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
    <th>Inactivate</th>
    <th><a href="<?=sortLink('Client.clientId', $this->viewVars)?>">Client ID</a></th>
    <th><a href="<?=sortLink('PackagePromoRel.packageId', $this->viewVars)?>">Pkg . ID</a></th>
    <th>Styles</th>
    <th><a href="<?=sortLink('Client.name', $this->viewVars)?>">Client Name</a></th>
    <th><a href="<?=sortLink('OfferLive.endDate', $this->viewVars)?>">End Date</a></th>
    <th>Ticket #</th>
    <th>Purchase Price</th>
    <th>Disc.</th>
    <th style="background-color: #7f0000; color: #fff">#Pkg</th>
    <th style="background-color: #7f0000; color: #fff">Disc.</th>
    <th style="background-color: #7f0000; color: #fff">Collected</th>
  </tr>
  </thead>
  <tbody class="rowBorderDark">
	<?php $grandTickets = $grandDiscounts = $grandCollected = $totalTickets = $totalDiscounts = $totalCollected = 0; ?>
  <?php foreach($activePackages as $k => $package): ?>
	<?php 
			$numTickets = count($package['Ticket']);

			$totalTickets += $numTickets;
			$totalDiscounts += $numTickets * 150;
			
			foreach($package['Ticket'] as $ticket ){ //for multiple tickets
				$totalCollected += $ticket['Ticket']['billingPrice'];
				$grandCollected += $ticket['Ticket']['billingPrice'];
			}
			
			$grandTickets += $numTickets;
			$grandDiscounts += $numTickets * 150;
	?>
	<?php echo $this->renderElement('package_promo_rels/mastercard_row', array('k' => $k, 'package' => $package, 'checkboxAction' => 'inactivate', 'numTickets' => $numTickets)) ?>
   <?php endforeach; ?>
   </tbody>
   <tfoot class="rowBorderDark">
  <tr>
    <td colspan="9"><div style="float: left; clear: none;"><a href="#" onclick="javascript:$('ActivateInactivateForm').submit(); return false;">Submit</a></div>
	<div style="float: right; clear: none; font-weight: bold;" class="textRed">TOTAL FOR LIVE PACKAGES</div>
	</td>
    <th><?=$totalTickets?></th>
    <th>$<?=$totalDiscounts?></th>
    <th>$<?=$totalCollected?></th>
  </tr>
  </tfoot>
</table>
<div class="collapsible">
<div class="handle">Inactive Packages</div>
<div class="collapsibleContent">
	<table border="0" style="margin-bottom: 30px; border: 0">
	<thead>
	  <tr class="rowBorderDark">
	    <th>Inactivate</th>
	    <th><a href="<?=sortLink('Client.clientId', $this->viewVars)?>">Client ID</a></th>
	    <th><a href="<?=sortLink('PackagePromoRel.packageId', $this->viewVars)?>">Pkg . ID</a></th>
	    <th>Styles</th>
	    <th><a href="<?=sortLink('Client.name', $this->viewVars)?>">Client Name</a></th>
	    <th><a href="<?=sortLink('OfferLive.endDate', $this->viewVars)?>">End Date</a></th>
	    <th>Ticket #</th>
	    <th>Purchase Price</th>
	    <th>Disc.</th>
	    <th style="background-color: #7f0000; color: #fff">#Pkg</th>
	    <th style="background-color: #7f0000; color: #fff">Disc.</th>
	    <th style="background-color: #7f0000; color: #fff">Collected</th>
	  </tr>
	  </thead>
	  <tbody class="rowBorderDark">
		<?php $totalTickets = $totalDiscounts = $totalCollected = 0; ?>
	  <?php foreach($inactivePackages as $k => $package):?>
		<?php 
				$numTickets = count($package['Ticket']);

				$totalTickets += $numTickets;
				$totalDiscounts += $numTickets * 150;

				foreach($package['Ticket'] as $ticket ){ //for multiple tickets
					$totalCollected += $ticket['Ticket']['billingPrice'];
					$grandCollected += $ticket['Ticket']['billingPrice'];
				}
				
				$grandTickets += $numTickets;
				$grandDiscounts += $numTickets * 150;
		?>
		<?php echo $this->renderElement('package_promo_rels/mastercard_row', array('k' => $k, 'package' => $package, 'checkboxAction' => 'activate', 'numTickets' => $numTickets)) ?>
	   <?php endforeach; ?>
	   </tbody>
	   <tfoot class="rowBorderDark">
	  <tr>
	    <td colspan="9"><div style="float: left; clear: none;"><a href="#" onclick="javascript:$('ActivateInactivateForm').submit(); return false;">Submit</a></div>
		<div style="float: right; clear: none; font-weight: bold;" class="textRed">TOTAL FOR INACTIVE PACKAGES</div>
		</td>
	    <th><?=$totalTickets?></th>
	    <th>$<?=$totalDiscounts?></th>
	    <th>$<?=$totalCollected?></th>
	  </tr>
	  </tfoot>
	</table>
</div></div>

<table border="0" style="margin-bottom: 30px; border: 0">
<thead>
  <tr class="rowBorderDark">
    <th colspan=9 width="100%" style="background-color: #000">&nbsp;</th>
    <th style="background-color: #000; color: #fff">#Pkg</th>
    <th style="background-color: #000; color: #fff">Disc.</th>
    <th style="background-color: #000; color: #fff">Collected</th>
  </tr>
  </thead>
   <tfoot class="rowBorderDark">
  <tr>
    <td colspan="9">
	<div style="float: right; clear: none; font-weight: bold;" class="textRed">GRAND TOTAL</div>
	</td>
    <th style="background-color: #7f0000"><?=$grandTickets?></th>
    <th style="background-color: #7f0000">$<?=$grandDiscounts?></th>
    <th style="background-color: #7f0000">$<?=$grandCollected?></th>
  </tr>
  </tfoot>
</table>
<?php echo $form->end();?>
</div>

<?php
echo $prototip->renderTooltips();
?>