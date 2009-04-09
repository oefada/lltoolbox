<style>
#ClientName {
	width: 500px;
}
td, th {
	padding: 5px;
}
</style>
<div class='advancedSearch' style="width: 800px">
	<?php echo $form->create('Client', array('url' => '/reports/car'))?>
<fieldset>
<h3 class='title'>GENERATE CLIENT ACTIVITY REPORT BY:</h3>

<div class="fieldRow">
	<div style="float: left; clear: none;">
	<?php
	echo $strictAutocomplete->autoComplete('clientName', '/clients/auto_complete', array('indicator' => 'clientSearchSpinner'));
	?>
	</div>
	<div style="float: left; clear: none; display: none" id="clientSearchSpinner"><img src="/img/spinner_small.gif"></div>
</div>

<?php if(isset($clientDetails)):?>
<div class="fieldRow">
	<label>Country, State, City</label><?=$clientDetails['Client']['locationDisplay']?>
</div>

<div class="fieldRow">
	<label>Current LOA start date</label><?=date('M d, Y', strtotime($clientDetails['Loa']['startDate'])).$html2->c($clientDetails['Loa']['loaId'])?>
</div>

<div class="fieldRow">
	<label>Membership Fee</label><?=$number->currency($clientDetails['Loa']['membershipFee'])?>
</div>

<div class="fieldRow lastRow">
	<label>Account Manager</label><?=$clientDetails['Client']['managerUsername']?>
</div>
<?php endif; ?>
</fieldset>

<?php echo $form->submit('Search') ?>
</div>

<?if(isset($clientDetails)):?>

<table border="1">
  <tr>
    <td>&nbsp;</td>
	<?php for($i = 0; $i <= 12; $i++)
			echo "<th class='blackBg'>".$monthNames[$i]."</th>";
	?>
    <td>Last 12 Months</td>
  </tr>
  <tr>
    <th><strong>phone</strong></th>
    <?php for($i = 0; $i <= 12; $i++)
			echo "<td>".@$results[$months[$i]]['phone']."</td>";
	?>
    <td><?=@$totals['phone']?></td>
  </tr>
  <tr class="altrow">
    <th><strong>web</strong></th>
    <?php for($i = 0; $i <= 12; $i++)
			echo "<td>".@$results[$months[$i]]['webRefer']."</td>";
	?>
    <td><?=@$totals['webRefer']?></td>
  </tr>
  <tr>
    <th><strong>portfolio</strong></th>
    <?php for($i = 0; $i <= 12; $i++)
			echo "<td>".@$results[$months[$i]]['productView']."</td>";
	?>
    <td><?=@$totals['productView']?></td>
  </tr>
  <tr class="altrow">
    <th><strong>search</strong></th>
    <?php for($i = 0; $i <= 12; $i++)
			echo "<td>".@$results[$months[$i]]['searchView']."</td>";
	?>
    <td><?=@$totals['searchView']?></td>
  </tr>
  <tr>
    <th><strong>home/destination</strong></th>
    <?php for($i = 0; $i <= 12; $i++)
			echo "<td>".@$results[$months[$i]]['destinationView']."</td>";
	?>
    <td><?=@$totals['destinationView']?></td>
  </tr>
  <tr class="altrow">
    <th><strong>email</strong></th>
    <?php for($i = 0; $i <= 12; $i++)
			echo "<td>".@$results[$months[$i]]['email']."</td>";
	?>
    <td><?=@$totals['email']?></td>
  </tr>
  <tr>
    <th>&nbsp;</th>
    <?php for($i = 0; $i <= 12; $i++)
			echo "<td>&nbsp;</td>";
	?>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <th><strong>auctions sold</strong></th>
    <?php for($i = 0; $i <= 12; $i++)
			echo "<td>&nbsp;</td>";
	?>
    <td>0</td>
  </tr>
  <tr>
    <th><strong>auctions $$</strong></th>
    <?php for($i = 0; $i <= 12; $i++)
			echo "<td>&nbsp;</td>";
	?>
    <td>0</td>
  </tr>
  <tr>
    <th>&nbsp;</th>
    <?php for($i = 0; $i <= 12; $i++)
			echo "<td>&nbsp;</td>";
	?>
    <td>0</td>
  </tr>
  <tr>
    <th><strong>fixed price sold</strong></th>
    <?php for($i = 0; $i <= 12; $i++)
			echo "<td>&nbsp;</td>";
	?>
    <td>0</td>
  </tr>
  <tr>
    <th><strong>fixed price $$</strong></th>
    <?php for($i = 0; $i <= 12; $i++)
			echo "<td>&nbsp;</td>";
	?>
    <td>0</td>
  </tr>
  <tr>
    <th>&nbsp;</th>
    <?php for($i = 0; $i <= 12; $i++)
			echo "<td>&nbsp;</td>";
	?>
    <td>0</td>
  </tr>
  <tr>
    <th><strong>$$ remitted</strong></th>
    <?php for($i = 0; $i <= 12; $i++)
			echo "<td>&nbsp;</td>";
	?>
    <td>0</td>
  </tr>
</table>
<? endif; ?>