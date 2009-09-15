<div class="tracks form">
	<?php echo $ajax->form('add', 'post', array('url' => "/tracks/edit/{$this->data['Track']['trackId']}", 'update' => 'MB_content', 'model' => 'Track', 	'complete' => 'closeModalbox()'));?>
	<fieldset>
	<?php
		echo $form->input('trackId', array('type' => 'hidden'));
		echo $form->input('loaId', array('type' => 'hidden'));
		echo $form->input('trackName');
		echo $form->input('revenueModelId');
	?>
	<fieldset id="revenueModelCriteria">
		<?php if (!isset($this->data['Track']['revenueModelId']) || $this->data['Track']['revenueModelId'] == 1): ?>
			<?php echo $this->render('_revenue_split_form'); ?>
		<?php elseif ($this->data['Track']['revenueModelId'] == 4): ?>
			<?php echo $this->render('_xy_commission_form'); ?>
		<?php else: ?>
			<?php echo $this->render('_xy_form'); ?>
		<?php endif ?>
	</fieldset>
	<?php
		echo $form->input('expirationCriteriaId', array('label' => 'Track Type'));
	?>
	<fieldset id="expirationCriteria">
		<?php
		$this->hasRendered = false;
		if(is_numeric($this->data['Track']['expirationCriteriaId'])) {
			$expCriteriaIdForm = $this->data['Track']['expirationCriteriaId'];
		} else {
			$expCriteriaIdForm = 1;
		}
			echo $this->render('_exp_criteria_'.$expCriteriaIdForm); ?>
	</fieldset>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>

<?php echo $ajax->observeField(
               "TrackRevenueModelId",
               array(
                  "update"=>"revenueModelCriteria",
                  "url"=>"/tracks/revenue_model_criteria_form",
				  'complete' => 'closeModalbox()'
               )
          )
?>

<?php echo $ajax->observeField(
               "TrackExpirationCriteriaId",
               array(
                  "update"=>"expirationCriteria",
                  "url"=>"/tracks/expiration_criteria_form",
				 'complete' => 'closeModalbox()'
               )
          )
?>
<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>
