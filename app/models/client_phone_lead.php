<?php
class ClientPhoneLead extends AppModel
{
	public $useTable = 'client_phone_leads';

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
	 * 
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
}
?>
