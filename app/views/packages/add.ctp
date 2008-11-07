<?php
$this->pageTitle = $client['Client']['name'].$html2->c($client['Client']['clientId'], 'Client Id:');
?>
<div class="packages form">
<?php echo $form->create('Package', array('url' => "/clients/$clientId/packages/add", 'id'=>'PackageAddForm'));?>

<?php echo $this->renderElement('../packages/_add_step_1'); ?>
<?php echo $this->renderElement('../packages/_add_step_2'); ?>
<?php echo $this->renderElement('../packages/_add_step_3'); ?>
<?php echo $this->renderElement('../packages/_add_step_4'); ?>
<?php echo $this->renderElement('../packages/_add_step_5'); ?>
<?php echo $this->renderElement('../packages/_add_step_6'); ?>

<?php echo $form->end('Submit');?>
</div>