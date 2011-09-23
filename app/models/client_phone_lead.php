<?php
class ClientPhoneLead extends AppModel
{
	/**
	 * Database table this model will use
	 * 
	 * @access	public
	 * @param	string
	 */
	public $useTable = 'client_phone_leads';

	/**
	 * Array of CSV File Headers mapped to a db table column
	 * 
	 * @access	public
	 * @param	string
	 */
	public $tableColums = array(
		'Client ID' => 'client_id',
		'Site' => 'site',
		'Time Initiated' => 'date',
		'Duration' => 'duration',
		'Published Number' => 'published_number',
		'Destination Number' => 'destination_number',
		"Caller's Number" => 'caller_number',
		"Caller's Name" => 'caller_name',
		"Caller's Location" => 'caller_location',
		'Country' => 'country',
		'Median Household Income' => 'median_household_income',
		'Per Capita Income' => 'per_capita_income',
		'Median Earnings' => 'median_earnings'
	);
	
	
	/**
	 * Build an array to later be saved from CSV data
	 * 
	 * @access	public
	 * @param	string csv data
	 * @return	array
	 */
	public function buildArrayFromCSVData($data)
	{
		$columns = array_shift($data);
		$client_phone_lead_record = array();
		foreach($data as $row_number => $row) {
			foreach($this->tableColums as $key => $value) {
				$client_phone_lead_record[$row_number][$value] = $row[array_search($key, $columns)];
			}
			switch($client_phone_lead_record[$row_number]['site']) {
				case 'luxury link':
					$client_phone_lead_record[$row_number]['site_id'] = 1;
					break;
				case 'family getaway':
					$client_phone_lead_record[$row_number]['site'] = 2;
					break;
			}
			unset($client_phone_lead_record[$row_number]['site']);
			$client_phone_lead_record[$row_number]['date'] = date('Y-m-d H:i:s', strtotime($client_phone_lead_record[$row_number]['date']));
		}
		return $client_phone_lead_record;
	}
	
	/**
	 * Check that the upload data is valid
	 * 
	 * @access	public
	 * @param	array upload_data
	 * @return	boolean
	 */
	public function uploadIsValid($upload_data)
	{
		// Assume data is valid
		$data_is_valid = true;
		
		// Check for data that makes the data invalid
		if ($upload_data['type'] != 'text/csv') {
			$data_is_invalid = false;
		} else if ($upload_data['error'] != 0) {
			$data_is_invalid = false;
		} else if (!($upload_data['size'] > 0)) {
			$data_is_invalid = false;
		}

		return $data_is_valid;
	}
}
?>
