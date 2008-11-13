<?php
$this->pageTitle = $client['Client']['name'].$html2->c($client['Client']['clientId'], 'Client Id:');
?>
<div class="packages form">
<?php echo $form->create('Package', array('url' => "/clients/$clientId/packages/add", 'id'=>'PackageAddForm'));?>

<?php echo $this->renderElement('../packages/_add_step_1'); ?>
<?php echo $this->renderElement('../packages/_setup'); ?>
<?php echo $this->renderElement('../packages/_merchandising'); ?>

<?php echo $form->end('Submit');?>
</div>