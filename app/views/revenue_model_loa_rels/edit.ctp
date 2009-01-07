<div class="revenueModelLoaRels form">
	<?php echo $ajax->form('add', 'post', array('url' => "/revenue_model_loa_rels/edit/{$this->data['RevenueModelLoaRel']['revenueModelLoaRelId']}", 'update' => 'MB_content', 'model' => 'RevenueModelLoaRel', 	'complete' => 'closeModalbox()'));?>
	<fieldset>
	<?php
		echo $form->input('revenueModelLoaRelId', array('type' => 'hidden'));
		echo $form->input('loaId', array('type' => 'hidden'));
		echo $form->input('revenueModelId');
	?>
	<fieldset id="revenueModelCriteria">
		<?php if (!isset($this->data['RevenueModelLoaRel']['revenueModelId']) || $this->data['RevenueModelLoaRel']['revenueModelId'] == 1): ?>
			<?php echo $this->render('_revenue_split_form'); ?>
		<?php else: ?>
			<?php echo $this->render('_xy_form'); ?>
		<?php endif ?>
	</fieldset>
	<?php
		echo $form->input('expirationCriteriaId');
	?>
	<fieldset id="expirationCriteria">
		<?php
		$this->hasRendered = false;
		if(is_numeric($this->data['RevenueModelLoaRel']['expirationCriteriaId'])) {
			$expCriteriaIdForm = $this->data['RevenueModelLoaRel']['expirationCriteriaId'];
		} else {
			$expCriteriaIdForm = 1;
		}
			echo $this->render('_exp_criteria_'.$expCriteriaIdForm); ?>
	</fieldset>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>

<?php echo $ajax->observeField(
               "RevenueModelLoaRelRevenueModelId",
               array(
                  "update"=>"revenueModelCriteria",
                  "url"=>"/revenue_model_loa_rels/revenue_model_criteria_form",
				  'complete' => 'closeModalbox()'
               )
          )
?>

<?php echo $ajax->observeField(
               "RevenueModelLoaRelExpirationCriteriaId",
               array(
                  "update"=>"expirationCriteria",
                  "url"=>"/revenue_model_loa_rels/expiration_criteria_form",
				 'complete' => 'closeModalbox()'
               )
          )
?>
<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>