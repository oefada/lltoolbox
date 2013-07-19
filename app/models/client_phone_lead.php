<?php
class ClientPhoneLead extends AppModel
{
    /**
     * Database table this model will use
     *
     * @access public
     * @param string
     */
    public $useTable = 'client_phone_leads';

    /**
     * Array of CSV File Headers mapped to a db table column
     *
     * @access public
     * @param string
     */
    public $tableColums = array(
        'Customer Code' => 'client_id',
        'Ad Source' => 'site',
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
    );

    /**
     * Build an array to later be saved from CSV data
     *
     * @access public
     * @param string csv data
     * @return array
     */
    public function buildArrayFromCSVData($data)
    {
        $columns = array_shift($data);
        $client_phone_lead_record = array();
        foreach ($data as $row_number => $row) {
            foreach ($this->tableColums as $key => $value) {
                $client_phone_lead_record[$row_number][$value] = $row[array_search($key, $columns)];
            }
            if (!$client_phone_lead_record[$row_number]['client_id']) {
                unset($client_phone_lead_record[$row_number]);
                continue;
            }

            $client_phone_lead_record[$row_number]['site_id'] = self::getSiteID(
                $client_phone_lead_record[$row_number]['site']
            );
            $client_phone_lead_record[$row_number]['date'] = self::formatDate(
                $client_phone_lead_record[$row_number]['date']
            );
            $client_phone_lead_record[$row_number]['caller_name'] = trim(
                strtolower(
                    $client_phone_lead_record[$row_number]['caller_first_name'] . ' ' . $client_phone_lead_record[$row_number]['caller_last_name']
                )
            );
            $client_phone_lead_record[$row_number]['published_number'] = self::formatPhoneNumber(
                $client_phone_lead_record[$row_number]['published_number']
            );
            $client_phone_lead_record[$row_number]['destination_number'] = self::formatPhoneNumber(
                $client_phone_lead_record[$row_number]['destination_number']
            );
            $client_phone_lead_record[$row_number]['caller_number'] = self::formatPhoneNumber(
                $client_phone_lead_record[$row_number]['caller_number']
            );
        }

        return $client_phone_lead_record;
    }

    /**
     * Check that the upload data is valid
     *
     * @access public
     * @param array upload_data
     * @return boolean
     */
    public function uploadIsValid($upload_data)
    {
        // Assume data is valid
        $data_is_valid = true;

        // Check for data that makes the data invalid
        if ($upload_data['type'] != 'text/csv') {
            $data_is_invalid = false;
        } else {
            if ($upload_data['error'] != 0) {
                $data_is_invalid = false;
            } else {
                if (!($upload_data['size'] > 0)) {
                    $data_is_invalid = false;
                }
            }
        }

        return $data_is_valid;
    }

    /**
     * Get a site id from a string
     *
     * @param string site_name
     * @return int
     */
    private static function getSiteId($site_name)
    {
        // default to luxurylink, siteId=1
        $site_id = 1;

        switch ($site_name) {
            case 'luxurylink':
                $site_id = 1;
                break;
            case 'family':
                $site_id = 2;
                break;
            default:
                $site_id = 1;
        }

        return $site_id;
    }

    /**
     * Format a date
     *
     * @param string date
     * @return string
     */
    private static function formatDate($date)
    {
        return date('Y-m-d H:i:s', strtotime($date));
    }

    /**
     * Remove non-numeric characters from a phone number
     *
     * @param string phone_number
     * @return string
     */
    private static function formatPhoneNumber($phone_number)
    {
        return ereg_replace("[^0-9]", '', $phone_number);
    }
}

?>
