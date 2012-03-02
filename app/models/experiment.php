<?php
class Experiment extends AppModel
{
	public $name = 'Experiment';
	public $useDbConfig = 'shared';
	public $useTable = 'experiments';
	public $displayField = 'name';

	/**
	 * Get a list of existing experiments
	 * 
	 * @access	public
	 * @param	int
	 * @return	array
	 */
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
				'Site.siteName',
				'SitesExperiments.status',
				'SitesExperiments.test_percentage',
				'SitesExperiments.created',
				'SitesExperiments.last_test'
			),
			'order' => array('Experiment.id')
		);
		if (!is_null($site_id)) {
			$params['conditions']['SitesExperiments.site_id'] = $site_id;
		}
		
		return $this->find('all', $params);
	}
	
	/**
	 * Updates an experiments status
	 * 
	 * @access	public
	 * @param	int
	 * @param	int
	 */
	public function updateStatus($experiment_id, $status_id)
	{
		$sql = "UPDATE sites_experiments SET status = $status_id WHERE experiment_id = $experiment_id";
		$this->query($sql);
	}

	/**
	 * Updates an experiments test percentage
	 * 
	 * @access	public
	 * @param	int
	 * @param	int
	 */
	public function updateTestPercentage($experiment_id, $pct)
	{
		$sql = "UPDATE sites_experiments SET test_percentage = $pct WHERE experiment_id = $experiment_id";
		$this->query($sql);
	}

	public function getTestPercentageByExperiemntId($experiment_id)
	{
		$sql = "SELECT test_percentage FROM sites_experiments WHERE experiment_id = $experiment_id";
		return $this->query($sql);
	}

	
	/**
	 * Builds a nice results array with calculations
	 * 
	 * @access	public
	 * @param	int
	 * @return	array
	 */
	public function getResults($experiment_id)
	{
		$results = array();
		$total_tests = $this->getTotalTestsForExperimentId($experiment_id);
		$total_conversions = $this->getTotalConversionsForExperimentId($experiment_id);
		$conversion_rate = floatval($total_conversions) / floatval($total_tests);
		$untested_treatments_tested = $this->getTestedCountByExperimentIdAndTreatmentName($experiment_id, 'untested');
		$untested_treatments_completed = $this->getCompletedCountByExperimentIdAndTreatmentName($experiment_id, 'untested');
		$untested_conversion_rate = ($untested_treatments_tested != 0) ? floatval($untested_treatments_completed) / floatval($untested_treatments_tested) : 0;
		$bot_treatments_tested = $this->getTestedCountByExperimentIdAndTreatmentName($experiment_id, 'bot');
		$bot_treatments_completed = $this->getCompletedCountByExperimentIdAndTreatmentName($experiment_id, 'bot');
		$bot_conversion_rate = ($bot_treatments_tested != 0) ? floatval($bot_treatments_completed) / floatval($bot_treatments_tested) : 0;
		$default_treatments_tested = $this->getTestedCountByExperimentIdAndTreatmentName($experiment_id, 'default');
		$default_treatments_completed = $this->getCompletedCountByExperimentIdAndTreatmentName($experiment_id, 'default');
		$default_conversion_rate = ($default_treatments_tested != 0) ? floatval($default_treatments_completed) / floatval($default_treatments_tested) : 0;
		$alt_treatments_tested = $this->getTestedCountByExperimentIdAndTreatmentName($experiment_id, 'alternate');
		$alt_treatments_completed = $this->getCompletedCountByExperimentIdAndTreatmentName($experiment_id, 'alternate');
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
			'alt_z_score' => $alt_z_score,
			'untested_treatments_tested' => $untested_treatments_tested,
			'untested_treatments_completed' => $untested_treatments_completed,
			'untested_conversion_rate' => $untested_conversion_rate * 100.0,
			'bot_treatments_tested' => $bot_treatments_tested,
			'bot_treatments_completed' => $bot_treatments_completed,
			'bot_conversion_rate' => $bot_conversion_rate * 100.0
		);

		return $results;
	}

	/**
	 * Gets the total number of tests run for an experiment
	 * 
	 * @access	public
	 * @param	int
	 * @return	int
	 */
	private function getTotalTestsForExperimentId($experiment_id)
	{
		$sql = "SELECT COUNT(1) AS TOTAL_TESTS FROM users_treatments WHERE experiment_id = $experiment_id";
		$total_tests = $this->query($sql);
		return (int) $total_tests[0][0]['TOTAL_TESTS'];
	}
	
	/**
	 * Gets the total number of conversions for an experiment
	 * 
	 * @access	public
	 * @param	int
	 * @return	int
	 */
	private function getTotalConversionsForExperimentId($experiment_id)
	{
		$sql = "SELECT COUNT(1) AS TOTAL_CONVERSIONS FROM users_treatments WHERE experiment_id = $experiment_id AND completed = 1";
		$total_conversions = $this->query($sql);
		return (int) $total_conversions[0][0]['TOTAL_CONVERSIONS'];		
	}

	/**
	 * Gets the total number of treatments for an experiment by name
	 * 
	 * @access	public
	 * @param	int
	 * @return	int
	 */
	private function getTestedCountByExperimentIdAndTreatmentName($experiment_id, $treatment_name)
	{
		$sql = "
			SELECT COUNT(1) AS TREATMENTS
			FROM users_treatments ut, treatments t
			WHERE
				ut.experiment_id = $experiment_id
				AND ut.treatment_id = t.id
				AND t.name = '$treatment_name'
		";
		$treatments_tested = $this->query($sql);
		return (int) $treatments_tested[0][0]['TREATMENTS'];
	}
	
	/**
	 * Gets the number of treatments completed
	 * for an experiment
	 * 
	 * @access	public
	 * @param	int
	 * @return	int
	 */
	private function getCompletedCountByExperimentIdAndTreatmentName($experiment_id, $treatment_name)
	{
		$sql = "
			SELECT COUNT(1) AS TREATMENTS
			FROM users_treatments ut, treatments t
			WHERE
				ut.experiment_id = $experiment_id
				AND ut.treatment_id = t.id
				AND ut.completed = 1
				AND t.name = '$treatment_name'
		";
		$treatments_tested = $this->query($sql);
		return (int) $treatments_tested[0][0]['TREATMENTS'];
	}

	/**
	 * Calculates the z-score (standard-deviation like) of an alternate
	 * experiment. Negative number is bad, positive number is good
	 * 
	 * @access	public
	 * @param	float
	 * @param	float
	 * @param	int
	 * @param	int
	 * @return	float
	 */
	private function calculateZscore($alt_conversion_rate, $default_conversion_rate, $alt_treatments_tested, $total_conversions)
	{
		return ($alt_conversion_rate - $default_conversion_rate) / sqrt( ( ($alt_conversion_rate * (1.0 - $alt_conversion_rate)) / floatval($alt_treatments_tested)) + ( ($default_conversion_rate * (1.0 - $default_conversion_rate)) / floatval($total_conversions)) );
	}
}
?>