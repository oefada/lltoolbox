<?php
App::import('Model', 'LltUserEvent');
App::import('Model', 'lltEventRollupByDay');

class EventRollupShell extends Shell
{
    /**
     * @var LltUserEvent $lltUserEvent
     */
    private $lltUserEvent;

    /**
     * @var LltEventRollupByDay $lltEventRollupByDay
     */
    private $lltEventRollupByDay;

    /**
     * @var string
     */
    private $logfile = 'event_rollup';

    /**
     *
     */
    public function initialize()
    {
        $this->lltUserEvent = new LltUserEvent;
        $this->lltEventRollupByDay = new LltEventRollupByDay;
    }

    /**
     *
     */
    public function main()
    {
        $startDate = isset($this->params['startDate']) ? $this->params['startDate'] : date('Y-m-d', strtotime('yesterday'));
        $endDate = isset($this->params['endDate']) ? $this->params['endDate'] : date('Y-m-d', strtotime('yesterday'));
        $dates = $this->getFirstRunDates($startDate, $endDate);
        foreach($dates as $date) {
            $startDate = $date . ' 00:00:00';
            $endDate = $date . ' 23:59:59';
            $eventRollupData = $this->lltUserEvent->eventRollupBySourceId($startDate, $endDate);
            $this->lltEventRollupByDay->create();
            $this->log($this->lltEventRollupByDay->saveAll($eventRollupData));
            unset($eventRollupData);
        }
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     */
    private function getFirstRunDates($startDate, $endDate)
    {
        $datesArray = array();
        while ($startDate != $endDate) {
            $datesArray[] = $startDate;
            $startDate = date("Y-m-d", strtotime("+1 day", strtotime($startDate)));
        }
        $datesArray[] = $endDate;

        return $datesArray;
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
