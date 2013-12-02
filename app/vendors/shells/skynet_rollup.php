<?php
App::import('Model', 'LltUserEvent');
App::import('Model', 'lltUserEventRollup');
App::import('Model', 'Client');
class SkynetRollupShell extends Shell
{
    private $lltUserEvent;
    private $lltUserEventRollup;
    private $Client;
    private $logfile = 'skynet_rollup';

    public function initialize()
    {
        $this->lltUserEvent = new LltUserEvent;
        $this->lltUserEventRollup = new lltUserEventRollup;
        $this->Client = new Client;
    }

    public function main()
    {
        if (isset($this->params['month']) && isset($this->params['year']) && isset($this->params['siteId'])) {
            $this->params['month'] = str_pad($this->params['month'], 2, 0, STR_PAD_LEFT);
            $startDate = $this->params['year'] . '-' . $this->params['month'] . '-01 00:00:00';
            $endDate = $this->params['year'] . '-' . $this->params['month'] . '-' . cal_days_in_month(CAL_GREGORIAN, $this->params['month'], $this->params['year']) . ' 23:59:59';
            $siteId = (int)$this->params['siteId'];
        } else if (isset($this->params['siteId'])) {
            $yesterday = date('Y-m-d', strtotime("yesterday"));
            $startDate = $yesterday . ' 00:00:00';
            $endDate = $yesterday . ' 23:59:59';
            $siteId = (int)$this->params['siteId'];
        } else {
            $this->log('siteId, month, and year are required parameters.');
            exit;
        }

        $clients = $this->lltUserEvent->getClientsWithEventDataBetweenDatesBySiteId($startDate, $endDate, $siteId);
        foreach ($clients as $key => $client) {
            $clients[$key] = array('client_id' => (int)$client['lltUserEvent']['clientId']);
        }
        $rollupData = array();

        foreach ($clients as $client) {
            $rollupData = $this->lltUserEvent->eventsByClient($client['client_id'], $siteId, $startDate, $endDate);
            if (!empty($rollupData)) {
                $numRecords = sizeof($rollupData);
                $this->lltUserEventRollup->create();
                if ($this->lltUserEventRollup->saveAll($rollupData)) {
                    $this->log("Saved $numRecords records for clientId: {$client['client_id']}, siteId: $siteId, startDate: $startDate, endDate: $endDate");
                } else {
                    $this->log("Could not save $numRecords records for clientId: {$client['client_id']}, siteId: $siteId, startDate: $startDate, endDate: $endDate");
                }
            } else {
                $this->log("No rollup data for clientId: {$client['client_id']}, siteId: $siteId, startDate: $startDate, endDate: $endDate");
            }
        }
    }

    /**
     * Utility method to log output
     *
     * @access    public
     * @param    string message to log
     */
    public function log($message)
    {
        parent::log($message, $this->logfile);
        echo date('Y-m-d H:i:s') . ' - ' . $message . "\n";
    }
}
