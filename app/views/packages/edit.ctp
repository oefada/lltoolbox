<?php
$this->pageTitle = $client['Client']['name'].$html2->c($client['Client']['clientId'], 'Client Id:');
?>
<div class="packages form">
<?php echo $form->create('Package', array('url' => "/clients/{$clientId}/packages/edit/{$this->data['Package']['packageId']}", 'id'=>'PackageAddForm'));?>

<?php echo $this->renderElement('../packages/_add_step_1'); ?>
<?php echo $this->renderElement('../packages/_setup'); ?>
<?php echo $this->renderElement('../packages/_merchandising'); ?>
<?php echo $form->input('Package.packageId'); ?>
<?php 
	foreach($this->data['ClientLoaPackageRel'] as $k => $v):
		echo $form->input('ClientLoaPackageRel.'.$k.'.clientLoaPackageRelId');
	endforeach;
?>
<input type='hidden' id='clone' name='data[clone]' value='' />
<div class='buttonrow'>
<?php echo $form->submit() ?>
<?php echo $form->submit('Clone Package', array('onclick' => '$("clone").value = "clone"')) ?>
</div>
<?php echo $form->end();?>

<h3>Package Promos</h3>
<div class="mB mT"><a href="/packages/<?php echo $package['Package']['packageId'];?>/packagePromos/add">Add New Package Promo</a></div>
<div class="mB">
	<table>
	<tr>
		<th>Package Promo Id</th>
		<th>Description</th>
		<th>Promo Code</th>
	</tr>
	<?php
	foreach ($package['PackagePromo'] as $k => $v) {
		echo '<tr>';
		echo '<td><a href="/packagePromos/edit/'. $v['packagePromoId'] . '">' . $v['packagePromoId'] . '</a></td>';
		echo '<td>' . $v['description'] . '</td>';
		echo '<td>' . $v['promoCode'] . '</td>';
		echo '</tr>';
	}
	?>
	</table>
</div>
</div>