<?php
class Bid extends AppModel
{
    public $name = 'Bid';
    public $useTable = 'bid';
    public $primaryKey = 'bidId';
    public $skipBeforeSaveFilter = false;

    public $belongsTo = array('User' => array('foreignKey' => 'userId'),
        'Offer' => array('foreignKey' => 'offerId')
    );

    /**
     * @return bool
     */
    public function beforeSave()
    {
        if ($this->skipBeforeSaveFilter == false) {
            $bidData = $this->data['Bid'];
            unset($this->data['Bid']);

            $this->data['Bid']['bidId'] = $bidData['bidId'];
            $this->data['Bid']['inactive'] = $bidData['inactive'];
        }

        return true;
    }

    /**
     * @return bool
     */
    public function beforeDelete()
    {
        return false;
    }

    /**
     * @param $offerId
     * @return array
     */
    public function getBidStatsForOffer($offerId)
    {
        $query = "
            SELECT COUNT(bidId) AS bidCount, MAX(bidAmount) AS winner
            FROM bid
            WHERE offerId = ?
        ";

        if ($bidStats = $this->query($query, array($offerId))) {
            return $bidStats;
        }
    }

    /**
     * @param $packageId
     * @return mixed
     */
    public function getBidStatsForPackageId($packageId)
    {
        $q = "SELECT COUNT(bidId) AS bidCount ";
        $q .= "FROM bid ";
        $q .= "INNER JOIN offerLuxuryLink as ot on (ot.offerId=bid.offerId) ";
        $q .= "WHERE packageId= ?";
        $bidStats = $this->query($q, array($packageId));

        return $bidStats[0][0]['bidCount'];
    }
}
