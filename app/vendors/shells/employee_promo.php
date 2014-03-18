<?php
error_reporting(0);
class EmployeePromoShell extends Shell
{
    public $uses = array('CreditTracking');
    private $csvFile = "/Users/mclifford/Dropbox/Documents/Downloads/employee_travel_benefits_2014.csv";

    /**
     *
     */
    public function _welcome() {}

    /**
     *
     */
    public function initialize()
    {
        parent::initialize();
    }

    /**
     *
     */
    public function main()
    {
        $handle = fopen($this->csvFile, 'r');

        while (($employeeData = fgetcsv($handle)) !== false) {
            $cofToApply = array(
                'CreditTracking' => array(
                    'creditTrackingTypeId' => 4,
                    'userId' => trim($employeeData[1]),
                    'amount' => trim($employeeData[2]),
                    'notes' => trim($employeeData[4])
                )
            );
            $this->CreditTracking->create();
            if ($this->CreditTracking->save($cofToApply)) {
                $this->out('Successfully applied the following CoF');
                $this->out("\t" . 'userId: ' . $cofToApply['CreditTracking']['userId']);
                $this->out("\t" . 'amount: ' . $cofToApply['CreditTracking']['amount']);
                $this->out("");
            }

        }
    }
}