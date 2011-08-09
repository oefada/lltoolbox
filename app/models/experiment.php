<?php
class Experiment extends AppModel
{
	public $name = 'Experiment';
	public $useDbConfig = 'shared';
	public $useTable = 'experiments';
	public $displayField = 'name';

	public function listExperiments($site_id = null)
	{
		$params = array(
			'joins' => array(
				array(
					'table' => 'sites_experiments',
					'alias' => 'SitesExperiments',
					'type' => 'left',
					'conditions' => array('Experiment.id = SitesExperiments.experiment_id')
				),
				array(
					'table' => 'sites',
					'alias' => 'Site',
					'type' => 'left',
					'conditions' => array('SitesExperiments.site_id = Site.siteId')
				)
			),
			'fields' => array(
				'Experiment.id',
				'Experiment.name',
				'Site.siteId',
				'Site.siteName'
			),
			'order' => array('Experiment.id')
		);
		if (!is_null($site_id)) {
			$params['conditions']['SitesExperiments.site_id'] = $site_id;
		}
		
		return $this->find('all', $params);
	}
	
	public function getResults($experiment_id)
	{
		$results = array();
		$total_tests = $this->getTotalTestsForExperimentId($experiment_id);
		$total_conversions = $this->getTotalConversionsForExperimentId($experiment_id);
		$conversion_rate = floatval($total_conversions) / floatval($total_tests);
		$default_treatments_tested = $this->getDefaultTreatmentsTestedForExperimentId($experiment_id);
		$default_treatments_completed = $this->getDefaultTreatmentsCompletedForExperimentId($experiment_id);
		$default_conversion_rate = ($default_treatments_tested != 0) ? floatval($default_treatments_completed) / floatval($default_treatments_tested) : 0;
		$alt_treatments_tested = $this->getAlternateTreatmentsTestedForExperimentId($experiment_id);
		$alt_treatments_completed = $this->getAlternateTreatmentsCompletedForExperimentId($experiment_id);
		$alt_conversion_rate = ($alt_treatments_tested != 0) ? floatval($alt_treatments_completed) / floatval($alt_treatments_tested) : 0;
		$alt_z_score = ($total_conversions != 0) ? $this->calculateZscore($alt_conversion_rate, $default_conversion_rate, $alt_treatments_tested, $total_conversions) : 0;

		$results = array(
			'experiment_name' => $this->field('Experiment.name', array('Experiment.id' => $experiment_id)), 
			'total_tests' => $total_tests,
			'total_conversions' => $total_conversions,
			'conversion_rate' => $conversion_rate * 100.0,
			'default_treatments_tested' => $default_treatments_tested,
			'default_treatments_completed' => $default_treatments_completed,
			'default_conversion_rate' => $default_conversion_rate * 100.0,
			'default_z_score' => 'control',
			'alt_treatments_tested' => $alt_treatments_tested,
			'alt_treatments_completed' => $alt_treatments_completed,
			'alt_conversion_rate' => $alt_conversion_rate * 100.0,
			'alt_z_score' => $alt_z_score
		);

		return $results;
	}

	private function getTotalTestsForExperimentId($experiment_id)
	{
		$sql = "SELECT COUNT(1) AS TOTAL_TESTS FROM users_treatments WHERE experiment_id = $experiment_id";
		$total_tests = $this->query($sql);
		return (int) $total_tests[0][0]['TOTAL_TESTS'];
	}
	
	private function getTotalConversionsForExperimentId($experiment_id)
	{
		$sql = "SELECT COUNT(1) AS TOTAL_CONVERSIONS FROM users_treatments WHERE experiment_id = $experiment_id AND completed = 1";
		$total_conversions = $this->query($sql);
		return (int) $total_conversions[0][0]['TOTAL_CONVERSIONS'];		
	}
	
	private function getDefaultTreatmentsTestedForExperimentId($experiment_id)
	{
		$sql = "
			SELECT COUNT(1) AS DEFAULT_TREATMENTS
			FROM users_treatments ut, treatments t
			WHERE
				ut.experiment_id = $experiment_id
				AND ut.treatment_id = t.id
				AND t.name = 'default'
		";
		$default_treatments_tested = $this->query($sql);
		return (int) $default_treatments_tested[0][0]['DEFAULT_TREATMENTS'];
	}
	
	private function getDefaultTreatmentsCompletedForExperimentId($experiment_id)
	{
		$sql = "
			SELECT COUNT(1) AS DEFAULT_TREATMENTS_COMPLETED
			FROM users_treatments ut, treatments t
			WHERE
				ut.experiment_id = $experiment_id
				AND ut.treatment_id = t.id
				AND ut.completed = 1
				AND t.name = 'default'				
		";
		$default_treatments_completed = $this->query($sql);
		return (int) $default_treatments_completed[0][0]['DEFAULT_TREATMENTS_COMPLETED'];
	}
	
	private function getAlternateTreatmentsTestedForExperimentId($experiment_id)
	{
		$sql = "
			SELECT COUNT(1) AS ALTERNATE_TREATMENTS
			FROM users_treatments ut, treatments t
			WHERE
				ut.experiment_id = $experiment_id
				AND ut.treatment_id = t.id
				AND t.name = 'alternate'
		";
		$alternate_treatments_tested = $this->query($sql);
		return (int) $alternate_treatments_tested[0][0]['ALTERNATE_TREATMENTS'];
	}
	
	private function getAlternateTreatmentsCompletedForExperimentId($experiment_id)
	{
		$sql = "
			SELECT COUNT(1) AS ALTERNATE_TREATMENTS_COMPLETED
			FROM users_treatments ut, treatments t
			WHERE
				ut.experiment_id = $experiment_id
				AND ut.treatment_id = t.id
				AND ut.completed = 1
				AND t.name = 'alternate'
		";
		$alternate_treatments_completed = $this->query($sql);
		return (int) $alternate_treatments_completed[0][0]['ALTERNATE_TREATMENTS_COMPLETED'];
	}
	
	private function calculateZscore($alt_conversion_rate, $default_conversion_rate, $alt_treatments_tested, $total_conversions)
	{
		return ($alt_conversion_rate - $default_conversion_rate) / sqrt( ( ($alt_conversion_rate * (1.0 - $alt_conversion_rate)) / floatval($alt_treatments_tested)) + ( ($default_conversion_rate * (1.0 - $default_conversion_rate)) / floatval($total_conversions)) );
	}
}
?>