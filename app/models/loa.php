<?php
class Loa extends AppModel
{
    public $name = 'Loa';
    public $useTable = 'loa';
    public $primaryKey = 'loaId';

    public $order = array("Loa.startDate DESC");
    public $actsAs = array('Containable', 'Logable');
    public $multisite = true;

    public $belongsTo = array(
        'Client' => array('foreignKey' => 'clientId'),
        'Currency' => array('foreignKey' => 'currencyId'),
        'LoaLevel' => array('foreignKey' => 'loaLevelId'),
        'LoaMembershipType' => array('foreignKey' => 'loaMembershipTypeId'),
        'AccountType' => array('foreignKey' => 'accountTypeId'),
        'LoaPaymentTerm' => array('foreignKey' => 'loaPaymentTermId'),
        'LoaInstallmentType' => array('foreignKey' => 'loaInstallmentTypeId')
    );

    public $hasMany = array(
        'LoaItem' => array('foreignKey' => 'loaId'),
        'ClientLoaPackageRel' => array('foreignKey' => 'loaId'),
        'Track' => array('foreignKey' => 'loaId'),
        'LoaPublishingStatusRel' => array('foreignKey' => 'loaId')
    );

    public $validate = array(
        'startDate' => array(
            'rule' => array('validateEndStartDate'),
            'message' => 'Start date must be less than end date'
        ),
        'endDate' => array(
            'rule' => array('validateEndStartDate'),
            'message' => 'Start date must be less than end date'
        ),
        'loaMembershipTypeId' => array(
            'rule' => array('comparison', '>', 0),
            'message' => 'Please select an LOA membership type'
        ),
        'sites' => array(
            'rule' => array('multiple', array('min' => 1)),
            'required' => true,
            'allowEmpty' => false,
            'message' => 'You must select a site.'
        ),
        'revenueSplitPercentage' => array(
            'numeric' => array(
                'rule' => 'numeric',
                'allowEmpty' => true,
                'message' => 'Numbers only'
            ),
            'greaterThanZero' => array(
                'rule' => array('comparison', '>', 0),
                'allowEmpty' => true,
                'message' => 'Please enter a value greater than 0'
            ),
            'lessThan100' => array(
                'rule' => array('comparison', '<', 100),
                'allowEmpty' => true,
                'message' => 'Please enter a value less than 100'
            )
        ),
        'averageDailyRate' => array(
            'numeric' => array(
                'rule' => 'numeric',
                'allowEmpty' => true,
                'message' => 'Numbers only'
            )
        )
    );

    /**
     * @param array $options
     * @return bool
     */
    public function beforeSave($options)
    {
        $this->data['Loa']['modified'] = date('Y-m-d h:i:s');
        if (isset($this->data['Loa']['loaId'])) {
            $this->saveLoaStatuses('PublishingStatusLL', 'LoaPublishingStatusRelLL', 'luxurylink');
            $this->saveLoaStatuses('PublishingStatusFG', 'LoaPublishingStatusRelFG', 'family');
        }
        unset($this->data['Loa']['PublishingStatus']);
        AppModel::beforeSave();
        return true;
    }

    /**
     *
     */
    public function afterSave()
    {
        if ($this->id == $this->get_current_loa($this->data['Loa']['clientId'])) {
            $this->Client->set_sites($this->data['Loa']['clientId'], $this->data['Loa']['sites']);
        }
        if (isset($this->data['Loa']['loaId'])) {
            if (!empty($this->data['LoaPublishingStatusRelLL'])) {
                foreach ($this->data['LoaPublishingStatusRelLL'] as $pStatus) {
                    $this->LoaPublishingStatusRel->create();
                    $this->LoaPublishingStatusRel->save($pStatus);
                }
            }
            if (!empty($this->data['LoaPublishingStatusRelFG'])) {
                foreach ($this->data['LoaPublishingStatusRelFG'] as $pStatus) {
                    $this->LoaPublishingStatusRel->create();
                    $this->LoaPublishingStatusRel->save($pStatus);
                }
            }
        }
        return;
    }

    /**
     *
     */
    public function afterDelete()
    {
        return;
    }

    /**
     * @return bool
     */
    public function validateEndStartDate()
    {
        $startDate = $this->data[$this->name]['startDate'];
        $endDate = $this->data[$this->name]['endDate'];

        if ($startDate >= $endDate) {
            return false;
        }
        return true;
    }

    /**
     * @param $client_id
     * @return int
     */
    public function get_current_loa($client_id)
    {
        $this->Loa->recursive = -1;
        $currentLoaId = $this->field(
            'loaId',
            array('Loa.clientId = ' . $client_id . ' AND now() BETWEEN Loa.startDate AND Loa.endDate')
        );
        if (empty($currentLoaId)) {
            $this->Client->recursive = -1;
            $client = $this->Client->findByClientId($client_id);
            if (empty($client['Client']['parentClientId'])) {
                $currentLoaId = $this->field(
                    'loaId',
                    array('Loa.clientId = ' . $client_id . ' AND Loa.loaLevelId = 0 AND now() < Loa.startDate')
                );
            } else {
                $currentLoaId = $this->field(
                    'loaId',
                    array('Loa.clientId =' . $client['Client']['parentClientId'] . ' AND now() BETWEEN Loa.startDate AND Loa.endDate')
                );
            }
            if (empty($currentLoaId)) {
                $currentLoaId = 0;
            }
        }
        return $currentLoaId;
    }

    public function get_current_loa_loalevel($client_id)
    {
        $this->recursive = -1;
        $params = array(
            'fields' => array(
                'Loa.loaId',
                'Loa.loaLevelId'
            ),
            'conditions' => array(
                'AND' => array(
                    'now() BETWEEN Loa.startDate AND Loa.endDate',
                    'Loa.clientId' => $client_id,
                ),
            )
        );

        $currentLoaId = $this->find('first', $params);

        if (empty($currentLoaId)) {
            $this->Client->recursive = -1;
            $client = $this->Client->findByClientId($client_id);

            if (!empty($client['Client']['parentClientId'])) {
                $params['conditions']['AND']['Loa.clientId'] = $client['Client']['parentClientId'];
                $currentLoaId = $this->find('first', $params);
            }
        }

        return (empty($currentLoaId) ? false : $currentLoaId);
    }

    public function get_loa_names()
    {
        $currentLoa = $this->LoaLevel->find(
            'list',
            array(
                'fields' => array('loaLevelId', 'loaLevelName'),
                'order' => 'sponsorship DESC'
            )
        );

        return $currentLoa;
    }

    public function getClientLoas($clientId)
    {
        if ($loas = $this->query(
            "SELECT * FROM loa Loa WHERE Loa.clientId = {$clientId} ORDER BY Loa.startDate DESC"
        )
        ) {
            return $loas;
        } else {
            if ($client = $this->query(
                "SELECT Client.parentClientId FROM client Client WHERE Client.clientId = {$clientId}"
            )
            ) {
                if ($loas = $this->query(
                    "SELECT * FROM loa Loa WHERE Loa.clientId = {$client[0]['Client']['parentClientId']} ORDER BY Loa.startDate DESC"
                )
                ) {
                    return $loas;
                }
            }
        }
        return array();
    }

    public function getClientLoasWithoutParentInfo($clientId)
    {
        if ($loas = $this->query(
            "SELECT * FROM loa Loa WHERE Loa.clientId = {$clientId} ORDER BY Loa.startDate DESC"
        )
        ) {
            return $loas;
        }
        return array();
    }

    public function getLoaClientId($loaId)
    {
        $query = "SELECT clientId FROM loa Loa WHERE Loa.loaId = {$loaId}";
        if ($clientId = $this->query($query)) {
            return $clientId[0]['Loa']['clientId'];
        }
    }

    public function getLoaOptionList($clientId)
    {
        $query = "SELECT loaId, startDate, endDate FROM loa Loa
                  WHERE clientId = {$clientId} AND Loa.endDate > NOW()";
        $list = array();
        if ($loas = $this->query($query)) {
            foreach ($loas as $loa) {
                $list[$loa['Loa']['loaId']] = $loa['Loa']['loaId'] . ': ' . date(
                        'M j, Y',
                        strtotime($loa['Loa']['startDate'])
                    ) . ' - ' . date('M j, Y', strtotime($loa['Loa']['endDate']));
            }
        }
        return $list;
    }

    private function saveLoaStatuses($siteArrayIndex, $siteSaveArrayIndex, $site)
    {
        if (!empty($this->data['Loa'][$siteArrayIndex])) {
            for ($i = 0; $i < 5; $i++) {
                $pStatus = $i + 1;
                // Let's see if we have a status already set in the database
                $thisStatus = $this->LoaPublishingStatusRel->find
                    (
                        'first',
                        array(
                            'conditions' => array(
                                'LoaPublishingStatusRel.loaId' => $this->data['Loa']['loaId'],
                                'LoaPublishingStatusRel.publishingStatusId' => $pStatus,
                                'LoaPublishingStatusRel.site' => $site
                            )
                        )
                    );
                // If this status was saved before and is still selected by user just remember the selection
                if ($thisStatus && in_array($pStatus, $this->data['Loa'][$siteArrayIndex])) {
                    // Remember what was saved in the database
                    $this->data[$siteSaveArrayIndex][$i] = $thisStatus;
                } // If this status was not saved before but selected by user this time create new status
                else {
                    if (in_array($pStatus, $this->data['Loa'][$siteArrayIndex])) {
                        $this->data[$siteSaveArrayIndex][$i]['loaId'] = $this->data['Loa']['loaId'];
                        $this->data[$siteSaveArrayIndex][$i]['publishingStatusId'] = $pStatus;
                        $this->data[$siteSaveArrayIndex][$i]['completedDate'] = date('Y-m-d H:i:s');
                        $this->data[$siteSaveArrayIndex][$i]['site'] = $site;
                    } // Otherwise clear the status
                    else {
                        $this->LoaPublishingStatusRel->deleteAll(
                            array(
                                'LoaPublishingStatusRel.loaId' => $this->data['Loa']['loaId'],
                                'LoaPublishingStatusRel.site' => $site,
                                'LoaPublishingStatusRel.publishingStatusId' => $thisStatus['LoaPublishingStatusRel']['publishingStatusId']
                            )
                        );
                    }
                }
            }
        } else {
            // Clear the statues
            $this->LoaPublishingStatusRel->deleteAll(
                array(
                    'LoaPublishingStatusRel.loaId' => $this->data['Loa']['loaId'],
                    'LoaPublishingStatusRel.site' => $site
                )
            );
        }
    }

    public function changeEmail($data, $subject = null)
    {
        Configure::write('debug', 0);
        $subj = "Loa Change";
        if (!empty($subject)) {
            $subj = $subject;
        }
        App::import('Helper', 'Html'); // loadHelper('Html'); in CakePHP 1.1.x.x
        $html = new HtmlHelper();
        //$text = "<h2>The following client's name has changed</h2>\n\n";

        $tbl = "<table cellpadding='2' cellspacing='1'>";
        $tbl .= $html->tableHeaders(
            array(
                '<b>Client Name</b>',
                '<b>AM</b>',
                '<b>Start Date</b>',
                '<b>Membership Fee</b>',
                '<b>Special Terms</b>'
            ),
            // array('class' => 'product_table'),
            array('style' => 'background-color:#CCC')
        );

        $startDateAsString = date('F d, Y', strtotime($this->deconstruct('startDate', $data['Loa']['startDate'])));
        $tbl .= $html->tableCells(
            array(
                $data['Client']['name'],
                $data['Client']['managerUsername'],
                $startDateAsString,
                $data['Loa']['membershipFee'],
                $data['Loa']['notes']
            )
        );
        $tbl .= "</table><br />\n";
        $text = 'http://' . $_SERVER['HTTP_HOST'] . '/loas/edit/' . $data['Loa']['loaId'];

        $msg = $tbl . $text;

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        // Additional headers
        $headers .= 'From: Toolbox <no-reply@luxurylink.com>' . "\r\n";
        if ($_SERVER['ENV'] == 'development' || ISSTAGE == true) {
            $to = "devmail@luxurylink.com";
            $headers .= 'Bcc: oefada@luxurylink.com' . "\r\n";
        } else {
            $to = "renew@luxurylink.com";
            //$headers .= 'Cc: accounting@luxurylink.com' . "\r\n";
        }
        @mail($to, $subj, $msg, $headers);
        return true;
    }

}

?>
