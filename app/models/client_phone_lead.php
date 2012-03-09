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
		'clientid' => 'client_id',
		'site' => 'site',
		'Datetime' => 'date',
		'Duration' => 'duration',
		'Tracking Number' => 'published_number',
		'Target Number' => 'destination_number',
		"Caller Number" => 'caller_number',
		"Caller First Name" => 'caller_first_name',
		"Last Name" => 'caller_last_name',
		"City" => "city",
		"State" => "state",
		"Zip" => "zip",
		'country' => 'country',
		"caller location" => "caller_location"
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
			if (!$client_phone_lead_record[$row_number]['client_id']) {
				unset($client_phone_lead_record[$row_number]);
				continue;
			}
			switch($client_phone_lead_record[$row_number]['site']) {
				case 'luxurylink':
					$client_phone_lead_record[$row_number]['site_id'] = 1;
					break;
				case 'family':
					$client_phone_lead_record[$row_number]['site'] = 2;
					break;
			}
			unset($client_phone_lead_record[$row_number]['site']);
			$client_phone_lead_record[$row_number]['date'] = date('Y-m-d H:i:s', strtotime($client_phone_lead_record[$row_number]['date']));
			$client_phone_lead_record[$row_number]['caller_name'] = trim(strtolower($client_phone_lead_record[$row_number]['caller_first_name'] . ' ' . $client_phone_lead_record[$row_number]['caller_last_name']));
			$client_phone_lead_record[$row_number]['published_number'] = ereg_replace("[^0-9]", "", $client_phone_lead_record[$row_number]['published_number']);
			$client_phone_lead_record[$row_number]['destination_number'] = ereg_replace("[^0-9]", "", $client_phone_lead_record[$row_number]['destination_number']);
			$client_phone_lead_record[$row_number]['caller_number'] = ereg_replace("[^0-9]", "", $client_phone_lead_record[$row_number]['caller_number']);
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
