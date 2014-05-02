<?php

class PromoCode extends AppModel
{

    public $name = 'PromoCode';
    public $useTable = 'promoCode';
    public $primaryKey = 'promoCodeId';
    public $displayField = 'promoCode';

    public $hasAndBelongsToMany = array(
        'Promo' =>
            array('className' => 'Promo',
                'foreignKey' => 'promoCodeId',
                'associationForeignKey' => 'promoId',
                'with' => 'promoCodeRel',
                'unique' => true,
            )
    );

    public $validate = array(
        'promoCode' => array(
            'rule1' => array(
                'rule' => array('custom', '/^[a-zA-Z0-9-_]*$/'),
                'allowEmpty' => false,
                'message' => 'Only letters, integers and dashes are allowed.'
            ),
            'rule2' => array(
                'rule' => array('__isDuplicatePromoCode'),
                'message' => 'The promoCode you entered already existed.'
            )
        )
    );

    /**
     * @return bool
     */
    public function beforeSave()
    {
        // check to make sure the promoCode does not already exist
        $this->data['PromoCode']['promoCode'] = strtoupper($this->data['PromoCode']['promoCode']);
        $result = $this->query('SELECT * FROM promoCode WHERE promoCode = "' . $this->data['PromoCode']['promoCode'] . '"');
        if (empty($result)) {
            return true;
        }
    }

    /**
     * @param $length
     * @return string
     */
    public function __generateCode($length)
    {
        $code = array();
        for ($i = 0; $i < $length + 1; $i++) {
            $code[$i] = rand(2, 35);
            if ($code[$i] > 9) {
                $code[$i] = chr(55 + $code[$i]);
                if ($code[$i] == 'I') {
                    $code[$i] = 'Z';
                }
                if ($code[$i] == 'O') {
                    $code[$i] = 'W';
                }
            }
        }
        return implode('', $code);
    }

    /**
     * @return bool
     */
    public function __isDuplicatePromoCode()
    {
        // check to make sure the promoCode does not already exist
        $this->data['PromoCode']['promoCode'] = strtoupper($this->data['PromoCode']['promoCode']);
        $result = $this->query('SELECT * FROM promoCode WHERE promoCode = "' . $this->data['PromoCode']['promoCode'] . '"');
        if (empty($result)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $code
     * @return bool
     */
    public function checkDuplicatePromoCode($code)
    {
        $result = $this->query('SELECT * FROM promoCode WHERE UPPER(promoCode) = "' . strtoupper($code) . '"');
        if (empty($result)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $prefix
     * @param $count
     * @param $promoId
     * @param int $length
     * @return int
     */
    public function generateMultipleCodes($prefix, $count, $promoId, $length = 5)
    {
        $created = 0;
        for ($i = 0; $i < $count; $i++) {
            $codeIsDuplicate = true;
            while ($codeIsDuplicate) {
                $thisCode = $prefix . $this->__generateCode($length);
                $codeIsDuplicate = $this->checkDuplicatePromoCode($thisCode);
            }
            $data = array();
            $data['Promo'] = array('promoId' => $promoId);
            $data['PromoCode'] = array('promoCode' => $thisCode);
            if ($this->saveAll($data)) {
                $created++;
            }
        }
        return $created;
    }

    /**
     * @param $prefix
     * @param $count
     * @param $promoId
     * @param int $length
     * @return array
     */
    public function generateReturnMultipleCodes($prefix, $count, $promoId, $length = 5)
    {
        $created = 0;
        $arrGeneratedCodes = array();
        for ($i = 0; $i < $count; $i++) {
            $codeIsDuplicate = true;
            while ($codeIsDuplicate) {
                $thisCode = $prefix . $this->__generateCode($length);
                $codeIsDuplicate = $this->checkDuplicatePromoCode($thisCode);
            }
            $data = array();
            $data['Promo'] = array('promoId' => $promoId);
            $data['PromoCode'] = array('promoCode' => $thisCode);
            if ($this->saveAll($data)) {
                $created++;
                $arrGeneratedCodes[] = $thisCode;
            }
        }
        return $arrGeneratedCodes;
    }

    /**
     * @param null $errorMsg
     * @return bool
     */
    private function isInvalidPromoCode($errorMsg = null)
    {
        $this->isValidPromoCode = false;
        if ($errorMsg) {
            $this->errors[] = $errorMsg;
        }
        return false;
    }

    /**
     * @param $promoCode
     * @param $userId
     * @param int $bidAmount
     * @param $offerId
     * @param $siteId
     * @param bool $topLevelDomain
     * @return bool
     */
    public function checkPromoCode($promoCode, $userId, $bidAmount = 0, $offerId, $siteId, $topLevelDomain = false)
    {
        $sql = "SELECT p.*, pc.promoCodeId, pc.promoCode, pc.inactive AS promoCodeInactive FROM promoCode pc ";
        $sql .= "INNER JOIN promoCodeRel pcr ON pc.promoCodeId = pcr.promoCodeId ";
        $sql .= "INNER JOIN promo p ON pcr.promoId = p.promoId ";
        $sql .= "WHERE pc.promoCode = '$promoCode' AND $bidAmount >= p.minPurchaseAmount ";
        $sql .= "AND NOW() BETWEEN p.startDate AND DATE_ADD(p.endDate, INTERVAL 1 DAY) LIMIT 1"; // make date range inclusive by adding another day to endDate

        $result = $this->query($sql);

        if (empty($result)) {
            return $this->isInvalidPromoCode(1);
        }

        $this->promoData = $result[0]['p'];
        $this->promoCodeData = $result[0]['pc'];
        $promoCodeId = $this->promoCodeData['promoCodeId'];

        // check to see if code is for specific user only
        $this->checkUsageForUser($promoCodeId, $userId);

        if ($this->hasPromoCodeRecipient && $this->isInvalidRecipient) {
            return $this->isInvalidPromoCode(2);
        }

        // check to see if promo requires new buyer for promo to apply
        if ($this->promoData['newBuyersOnly']) {
            $this->checkPreviousPurchases($userId);
            if ($this->isPreviousBuyer === true) {
                return $this->isInvalidPromoCode(3);
            }
        }

        // check to see if code was used already
        if ($this->promoData['oneUsagePerCode']) {
            if ($this->hasUsagePerCode($promoCodeId, $userId) === true) {
                return $this->isInvalidPromoCode(4);
            }
        }

        // check to see if this user has used this code before
        if ($this->promoData['oneUsagePerUser']) {
            if ($this->hasUsagePerUser($promoCodeId, $userId, $offerId) === true) {
                return $this->isInvalidPromoCode(5);
            }
        }

        $checkSiteId = intval($this->promoData['siteId']);
        if ($checkSiteId > 0 && $checkSiteId != $siteId) {
            return $this->isInvalidPromoCode('display|Please note there is a site restriction on this promotion.');
        }

        $checkTldId = intval($this->promoData['tldId']);
        if ($topLevelDomain && $topLevelDomain != $checkTldId) {
            return $this->isInvalidPromoCode('display|Please note there is a locale restriction on this promotion.');
        }

        // is promo code inactive?
        if ($this->promoCodeData['promoCodeInactive'] == 1) {
            return $this->isInvalidPromoCode(
                'display|Please note the promo code you have entered is not currently active.'
            );
        }

        if (intval($offerId) > 0) {
            $result = $this->getClientDetailsByOffer($offerId);
            $offerClientId = $result['clientId'];
            $offerClientTypeId = $result['clientTypeId'];

            // theme restrictions
            $themeCheck = $this->getThemeRestrictions($this->promoData['promoId']);
            if (sizeof($themeCheck) > 0) {
                $themeListIds = array();
                $themeListDescs = array();
                //array thememecheck = array of themes
                foreach ($themeCheck as $key => $values) {
                    //array values = themes
                    foreach ($values as $t) {
                        $themeListIds[] = $t['themeId'];
                        $themeListDescs[] = $t['themeName'];
                    }
                }
                if (!$this->isClientInTheme($offerClientId, $themeListIds)) {
                    return $this->isInvalidPromoCode(
                        'display|Please note there is a theme restriction on this promotion.'
                    );
                }
            }

            // destination restrictions
            $destCheck = $this->getDestinationRestrictions($this->promoData['promoId']);
            if (sizeof($destCheck) > 0) {
                $destListIds = array();
                $destListDescs = array();
                foreach ($destCheck as $dest) {
                    $destListIds[] = $dest['d']['destinationId'];
                    $destListDescs[] = $dest['d']['destinationName'];
                }

                if (!$this->isClientInDestination($offerClientId, $destListIds)) {
                    return $this->isInvalidPromoCode(
                        'display|Please note there is a destination restriction on this promotion.'
                    );
                }
            }

            // client restrictions
            $clientCheck = $this->getClientRestrictions($this->promoData['promoId']);
            if (sizeof($clientCheck) > 0) {
                $clientListIds = array();
                $clientListDescs = array();
                foreach ($clientCheck as $client) {
                    $clientListIds[] = $client['0']['clientId'];
                    $clientListDescs[] = $client['c']['name'];
                }

                if (!in_array($offerClientId, $clientListIds)) {
                    return $this->isInvalidPromoCode(
                        'display|Please note there is a client restriction on this promotion.'
                    );
                }
            }

            // client type restrictions
            $clientTypeCheck = $this->getClientTypeRestrictions($this->promoData['promoId']);
            if (sizeof($clientTypeCheck) > 0) {
                $clientTypeListIds = array();
                $clientTypeListDescs = array();
                foreach ($clientTypeCheck as $ct) {
                    $clientTypeListIds[] = $ct['0']['clientTypeId'];
                    $clientTypeListDescs[] = $ct['c']['clientTypeName'];
                }

                if (!in_array($offerClientTypeId, $clientTypeListIds)) {
                    return $this->isInvalidPromoCode(
                        'display|Please note there is a property type restriction on this promotion.'
                    );
                }
            }
        }

        // get the amount off for the bid and the adjusted promo price for the package
        if ($this->promoData['percentOff'] && !$this->promoData['amountOff']) {
            // for percent off
            $this->promoData['totalAmountOff'] = ($bidAmount * ($this->promoData['percentOff'] / 100));
        } elseif (!$this->promoData['percentOff'] && $this->promoData['amountOff']) {
            // for amount off
            $this->promoData['totalAmountOff'] = $this->promoData['amountOff'];
        }
        $this->promoData['promoAdjustedPrice'] = $bidAmount - $this->promoData['totalAmountOff'];

        $this->isValidPromoCode = true;
        return true;
    }

    /**
     * @param $promoCodeId
     * @param $userId
     * @return bool
     */
    public function hasUsagePerCode($promoCodeId, $userId)
    {
        // any other user has a promoOfferTracking record
        $sql = 'SELECT COUNT(*) AS nbr FROM promoOfferTracking ';
        $sql .= 'WHERE userId <> ' . $userId . ' AND promoCodeId = ' . $promoCodeId;

        $result = $this->query($sql);
        if ($result[0][0]['nbr'] > 0) {
            return true;
        }

        // any promoTicketRel record

        $sql = 'SELECT COUNT(*) AS nbr FROM promoTicketRel ';
        $sql .= 'WHERE promoCodeId = ' . $promoCodeId;

        $result = $this->query($sql);
        if ($result[0][0]['nbr'] > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param $promoCodeId
     * @param $userId
     * @param $offerId
     * @return bool
     */
    public function hasUsagePerUser($promoCodeId, $userId, $offerId)
    {

        $sql = 'SELECT COUNT(*) AS nbr FROM promoOfferTracking ';
        $sql .= 'WHERE userId = ' . $userId . ' AND promoCodeId = ' . $promoCodeId . ' AND offerId <> ' . $offerId;

        $result = $this->query($sql);
        // any other promoOfferTracking record for this user
        if ($result[0][0]['nbr'] > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param $promoCodeId
     * @param $userId
     */
    public function checkUsageForUser($promoCodeId, $userId)
    {
        // code is only allowed to be used by this user if promocode exists in this table

        $sql = 'SELECT userId FROM promoCodeRecipient ';
        $sql .= 'WHERE promoCodeId = ' . $promoCodeId;

        $result = $this->query($sql);
        if (!empty($result)) {
            $this->hasPromoCodeRecipient = true;
            $found_user = false;
            foreach ($result as $row) {
                if ($row['userId'] == $userId) {
                    $found_user = true;
                    break;
                }
            }
            if (!$found_user) {
                $this->isInvalidRecipient = true;
            }
        } else {
            $this->hasPromoCodeRecipient = false;
        }
    }

    /**
     * @param $userId
     */
    public function checkPreviousPurchases($userId)
    {
        // see if this user has previous tickets
        $sql = 'SELECT COUNT(*) AS COUNT FROM paymentDetail ';
        $sql .= 'WHERE isSuccessfulCharge = 1 AND userId = ' . $userId;

        $result = $this->query($sql);
        $this->isPreviousBuyer = ($result[0][0]['COUNT'] > 0) ? true : false;
    }

    /**
     * @param $userId
     * @param $offerId
     * @return bool
     */
    public function getPromoCodeOT($userId, $offerId)
    {

        $sql = 'SELECT pc.*, gc.giftCertBalanceId FROM promoOfferTracking pot';
        $sql .= 'INNER JOIN promoCode pc USING(promoCodeId)';
        $sql .= 'LEFT JOIN giftCertBalance gc ON gc.promoCodeId = pc.promoCodeId';
        $sql .= 'WHERE pot.userId = ' . $userId . ' AND pot.offerId = ' . $offerId . ' LIMIT 1';

        $result = $this->query($sql);
        if (!empty($result[0]['giftCertBalanceId'])) {
            $this->promoCodeIsGc = true;
        }
        return (!empty($result)) ? $result[0]['promoCode'] : false;
    }

    /**
     * @param $offerId
     * @return mixed
     */
    public function getClientDetailsByOffer($offerId)
    {
        $sql = "SELECT c.clientId, c.clientTypeId FROM offerLuxuryLink o INNER JOIN client c USING(clientId) ";
        $sql .= " where o.offerid = " . $offerId;

        $result = $this->query($sql);

        return $result[0]['c'];
    }

    /**
     * @param $promoId
     * @return mixed
     */
    public function getThemeRestrictions($promoId)
    {
        $sql = "SELECT t.themeId, t.themeName FROM toolbox.promoRestrictionTheme rt ";
        $sql .= "INNER JOIN theme t USING(themeId) ";
        $sql .= "WHERE rt.promoId = " . $promoId;

        $result = $this->query($sql);

        return $result;
    }

    /**
     * @param $client
     * @param $themes
     * @return bool
     */
    public function isClientInTheme($client, $themes)
    {
        $sql = "SELECT COUNT(*) AS nbr FROM clientThemeRel ";
        $sql .= "WHERE clientId = " . $client . " AND themeId IN (" . implode(
                ',',
                $themes
            ) . ")";

        $themeExists = $this->query($sql);
        if ($themeExists[0]['0']['nbr'] > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param $promoId
     * @return mixed
     */
    public function getDestinationRestrictions($promoId)
    {
        $sql = "SELECT d.destinationId, d.destinationName FROM toolbox.promoRestrictionDestination rd ";
        $sql .= "INNER JOIN destination d USING(destinationId) ";
        $sql .= " WHERE rd.promoId = " . $promoId;

        return $this->query($sql);
    }

    /**
     * @param $client
     * @param $destinations
     * @return bool
     */
    public function isClientInDestination($client, $destinations)
    {
        $sql = "SELECT COUNT(*) AS nbr FROM clientDestinationRel WHERE clientId = $client AND destinationId IN (" . implode(
                ',',
                $destinations
            ) . ")";
        $destExists = $this->query($sql);
        if ($destExists[0][0]['nbr'] > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param $promoId
     * @return mixed
     */
    public function getClientRestrictions($promoId)
    {
        $sql = "SELECT IFNULL(c.clientId, 0) AS clientId, c.name FROM toolbox.promoRestrictionClient rc ";
        $sql .= " LEFT JOIN client c USING(clientId) ";
        $sql .= "WHERE promoId = " . $promoId;

        return $this->query($sql);

    }

    /**
     * @param $promoId
     * @return mixed
     */
    public function getClientTypeRestrictions($promoId)
    {
        $sql = "SELECT IFNULL(c.clientTypeId, 0) AS clientTypeId, c.clientTypeName FROM toolbox.promoRestrictionClientType rc ";
        $sql .= " INNER JOIN clientType c USING(clientTypeId) ";
        $sql .= " WHERE promoId = " . $promoId;

        return $this->query($sql);
    }

    /**
     * @return bool
     */
    protected static function getEntityColumnsMap()
    {
        return false;
    }
}
