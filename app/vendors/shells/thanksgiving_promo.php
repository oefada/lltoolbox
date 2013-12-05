<?php
error_reporting(0);
class ThanksgivingPromoShell extends Shell
{
    public $uses = array('Bid', 'CreditTracking');

    private $startDate;
    private $endDate;
    private $applyCoF = false;

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
                if ($this->applyCoF === true) {
                    $this->CreditTracking->create();
                    if ($this->CreditTracking->save($cofToApply)) {
                        $this->out('Successfully applied the following CoF');
                        $this->out("\t" . 'userId: ' . $cofToApply['CreditTracking']['userId']);
                        $this->out("\t" . 'amount: ' . $cofToApply['CreditTracking']['amount']);
                        $this->out("");
                    }
                } else {
                    var_dump($cofToApply);
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

        $this->applyCoF = (isset($this->params['applyCoF'])) ? true : false;

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
            SUM(b.bidAmount) as bidAmount,
            SUM(CEIL(b.bidAmount * 0.10)) AS cofAmount,
            u.userId,
            u.firstName,
            u.lastName,
            u.email
            FROM
                bid b
            INNER JOIN `user` u ON b.userId = u.userId
            WHERE
                b.bidId IN (
                    SELECT
                        DISTINCT(b1.bidId)
                    FROM
                        bid b1
                    INNER JOIN ticket t ON t.offerId = b1.offerId
                    INNER JOIN paymentDetail pd ON pd.ticketId = t.ticketId
                    INNER JOIN `user` u1 ON b1.userId = u1.userId
                    INNER JOIN offerLuxuryLink ol ON b1.offerId = ol.offerId
                    WHERE
                        b1.winningBid = 1
                        AND b1.bidDatetime BETWEEN ? AND ?
                        AND pd.isSuccessfulCharge = 1
                        AND t.ticketStatusId NOT IN (7, 8, 16, 17, 18)
                        AND ol.clientId != 8455
                )
            GROUP BY u.userId
            ORDER BY u.email
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