<?php
error_reporting(0);
class ThanksgivingPromoShell extends Shell
{
    public $uses = array('Bid', 'CreditTracking');

    private $startDate;
    private $endDate;

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
        if (!$this->validateInput()) {
            die;
        }

        $winningBids = $this->getWinningBids();
        if (!empty($winningBids)) {
            $this->out('Processing ' . count($winningBids) . ' bids');

            foreach($winningBids as $winningBid) {
                $cofToApply = array(
                    'CreditTracking' => array(
                        'creditTrackingTypeId' => 4,
                        'userId' => $winningBid['userId'],
                        'amount' => $winningBid['cofAmount'],
                        'notes' => 'Automatic CoF: Thanksgiving 2013 Promo'
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
        } else {
            $this->out('There are no winning bids for the period ' . $this->startDate . ' to ' . $this->endDate);
        }
    }

    /**
     * @return bool
     */
    private function validateInput()
    {
        $errorFlag = false;

        if (!isset($this->params['startDate']) || !isset($this->params['endDate'])) {
            $this->out('Error: startDate and endDate are required parameters!');
            $errorFlag = true;
        } else if (!($this->params['startDate'] < $this->params['endDate'])) {
            $this->out('Error: startDate must be before endDate');
            $errorFlag = true;
        } else {
            $this->startDate = $this->params['startDate'];
            $this->endDate = $this->params['endDate'];
        }

        return !$errorFlag;
    }

    /**
     * @return array
     */
    private function getWinningBids()
    {
        $winningBids = array();

        $sql = "
          SELECT
            b.bidId,
            b.bidAmount,
            CEIL(b.bidAmount * 0.10) as cofAmount,
            u.userId,
            u.email
          FROM
            bid b
          INNER JOIN ticket t ON t.offerId = b.offerId
          INNER JOIN paymentDetail pd ON pd.ticketId = t.ticketId
          INNER JOIN `user` u ON b.userId = u.userId
          INNER JOIN offerLuxuryLink ol ON b.offerId = ol.offerId
          WHERE
            b.winningBid = 1
            AND b.bidDatetime BETWEEN ? AND ?
            AND pd.isSuccessfulCharge = 1
            AND t.ticketStatusId NOT IN (7, 8, 16, 17, 18)
            AND ol.clientId!=8455
        ";

        $winningBidsArray = $this->Bid->query($sql, array($this->startDate, $this->endDate));

        foreach($winningBidsArray as $winningBidArray) {
            $winningBids[] = array(
                'bidId'     => $winningBidArray['b']['bidId'],
                'bidAmount' => $winningBidArray['b']['bidAmount'],
                'cofAmount' => $winningBidArray[0]['cofAmount'],
                'userEmail' => $winningBidArray['u']['email'],
                'userId'    => $winningBidArray['u']['userId'],
            );
        }

        return $winningBids;
    }
}