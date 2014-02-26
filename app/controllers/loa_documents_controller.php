<?php
include(APP . 'vendors/php-name-parser/index.php');
class LoaDocumentsController extends AppController
{
    public $name = 'LoaDocuments';
    public $helpers = array('Html', 'Form', 'Ajax', 'Javascript', 'Time');
    public $uses = array(
        'LoaDocument',
        'Client',
        'Loa',
        'LoaMembershipType',
        'LoaPaymentTerm',
        'LoaInstallmentType',
        'ConnectorLog',
        'LoaDocumentSource',
    );
    public $components = array('RequestHandler');
    public function beforeFilter()
    {
        parent::beforeFilter();
        //allow access
        $this->LdapAuth->allow('*');
        //disable debug kit
        if (in_array(low($this->params['action']), array('save_document'))) {
            unset($this->components['DebugKit.Toolbar']);
            // cake 2+
            // $this->Components->unload('DebugKit.Toolbar');
        }
    }

    public function listall($id, $mode = null)
    {
        //make errors an array so later we can create several error conditions
        $errors = array();
        if (!isset($id)) {
            $errors[] = 'LoaId is Required';
            $code = 0;
        } else {
            $code = 1;
            $conditions = array("LoaDocument.loaId" => $id, "LoaDocument.active" => 1);
            $order = array("LoaDocument.loaDocumentId DESC");
            $documents = $this->LoaDocument->find('all', array('conditions' => $conditions, 'order' => $order));
            if (empty($documents)) {
                $errors[] = 'No documents found for LoaId ' . $id;
                $code = 0;
            }
        }
        $results = array(
            'response' => $code,
            'message' => $documents
        );
        if (!empty($errors)) {
            $results = array(
                'response' => $code,
                'message' => $errors
            );
        }
        $this->set('mode', $mode);
        $this->set('arrResponse', $results);
    }

    public function listProposalsClient($clientId)
    {

        $clientId = (int)$clientId;
        $conditions = array("LoaDocument.clientId" => $clientId,'LoaDocument.isProposal'=>1);
        $order = array("LoaDocument.loaDocumentId"=>"DESC");
        $params =array('conditions' => $conditions,'order'=>$order);

        //debug($documents);
        $this->paginate = array(
            'conditions' => $conditions,
            'limit' => 15,
            'page' => 1,
            'order'=>$order
        );

        $client =$this->Loa->Client->findByClientId($clientId);
        $documents =  $this->paginate('LoaDocument');

        $this->set('documents',$documents);
        $this->set('clientId', $clientId);
        $this->set('client', $client);
        $this->set('loa',$client);
        $this->set('currentTab','property');
    }

    /**Returns the PDF from the database and prompts download
     * @param int $loaId OR char36 sugarLoaId
     * @param int $loaDocumentId
     * @return bool or blob
     */

    public function download($loaId, $loaDocumentId)
    {
        set_time_limit(60);
        Configure::write('debug', 0);
        // do not use layout
        $this->layout = false;
        $this->pageTitle = false;
        $errors = array();
        if (!isset($loaDocumentId, $loaId)) {
            $errors[] = '-loaDocumentId and loaId not set';
            $this->set('errors', $errors);
            return false;
        }
        $conditions = array(
            "LoaDocument.loaDocumentId" => $loaDocumentId,
            "LoaDocument.active" => 1,
            "OR" => array(
                "LoaDocument.loaId" => $loaId,
                "LoaDocument.sugarLoaId" => $loaId
            )
        );
        $document = $this->LoaDocument->find('first', array('conditions' => $conditions));
        if (empty($document)) {
            $errors[] = '-Could not load document';
        }
        $client = $this->Client->findByClientId($document['LoaDocument']['clientId']);

        //special character handling for clientName with becomes part of filename.

        if (isset($document['LoaDocument']['companyName'])) {
            $document['LoaDocument']['companyName'] = $this->LoaDocument->remove_accents(
                $document['LoaDocument']['companyName']
            );
        }
        if (isset($client['Client']['name'])) {
            $client['Client']['name'] = $this->LoaDocument->remove_accents($client['Client']['name']);
        }
        $this->set('loaId', $loaId);
        $this->set('loaDocumentId', $loaDocumentId);

        $this->set('errors', $errors);
        $this->set('document', $document);
        $this->set('client', $client);
    }

    public function save_document($input = null)
    {

        $errors = array();
        $mode = $this->params['url']['ext'];
        $result = array();


        $input = $this->params['form'];

        if ('json' == $mode) {
            //if accessed with .json will automatically use json layout in views/loa_documents/json
            Configure::write('debug', 0);

            if (function_exists('xdebug_disable')) {
                xdebug_disable();
            }

            if (!isset($input['insData'])) {
                $errors['missingParam'] = 'Invalid Request. Please set insData parameter.';
            }


            // Convert JSON string into assoc. array
            if (!$decoded_request = json_decode($input['insData'], true)) {
                $errors['JsonDecoding'] = 'Invalid Json received';
            }

            $this->data['LoaDocument']['sugarLoaId'] = $decoded_request['loa']['id'];
        }
        if (!empty($this->data)) {

            if ('json' == $mode) {

                //start logging service
                $this->ConnectorLog->setData($input['insData']);
                //direction
                $this->ConnectorLog->setParam('direction', 0);

                //REGISTER VARIABLES FROM Sugar JSON OBJECT
                //Loa Document Fields
                $this->data['LoaDocument']['sugarLoaId'] = $decoded_request['loa']['id'];

                $sugarClientName = str_replace('&#039;', "'", $decoded_request['loa']['account_name']);
                $sugarClientName = $this->LoaDocument->utf8dec($sugarClientName);

                if (isset($decoded_request['loa']['assigned_user_name'])) {
                    $this->data['LoaDocument']['signerName'] = $decoded_request['loa']['assigned_user_name'];
                }
                if (isset($decoded_request['assigned_user']['title'])) {
                    $this->data['LoaDocument']['signerTitle'] = $decoded_request['assigned_user']['title'];
                }

                $this->data['LoaDocument']['contactPrefix'] = $decoded_request['loa']['salutation_c'];
                $this->data['LoaDocument']['contactName'] = $decoded_request['loa']['full_name_c'];
                $this->data['LoaDocument']['contactTitle'] = '';
                $this->data['LoaDocument']['docDate'] = $decoded_request['loa']['date_modified'];
                $this->data['LoaDocument']['generatedBy'] = $decoded_request['current_user']['user_name'];
                //important for giving docs right name
                $this->data['LoaDocument']['companyName'] = $sugarClientName;
                $this->data['LoaDocument']['isProposal'] = 1;
                $this->data['LoaDocument']['loaDocumentSourceId'] = 2;

                if (isset($decoded_request['client']['client_id'])){

                    $this->data['LoaDocument']['clientId'] = $decoded_request['client']['client_id'];
                }

                //register Loa Fields
                $loa['Client']['companyName'] = $sugarClientName;
                $loa['Client']['name'] = $sugarClientName;
                $loa['Client']['segment'] = $decoded_request['client']['segment'];

                //fees and dates
                $loa['Loa']['membershipFee'] = $decoded_request['loa']['agreement_fee_c'];
                $loa['Loa']['auctionCommissionPerc'] = $decoded_request['loa']['commission_buynow_c'];
                $loa['Loa']['buynowCommissionPerc'] = $decoded_request['loa']['commission_buynow_c'];
                $loa['Loa']['startDate'] = $decoded_request['loa']['effective_date_c'];
                $loa['Loa']['endDate'] = $decoded_request['loa']['expiration_date_c'];
                //
                $loa['Loa']['membershipTotalPackages']= $decoded_request['loa']['barterpackages_c'];
                $loa['Loa']['membershipTotalNights'] = $decoded_request['loa']['barternights_c'];
                $loa['Loa']['numEmailInclusions'] = $decoded_request['loa']['number_of_emails_c'];
                $loa['Loa']['revenueSplitPercentage'] = $decoded_request['loa']['revsplit_c'];

                $loa['Loa']['notes'] = nl2br($decoded_request['loa']['special_instructions_c']);

                //fields to translate
                if (isset($decoded_request['loa']['payment_category_c'])) {
                    /**$loa['Loa']['loaMembershipTypeId'] = $this->LoaMembershipType->getMemberShipTypeIDbyName(
                        $decoded_request['loa']['payment_category_c']
                    );*/
                    $loa['Loa']['loaMembershipTypeId'] = intval(trim($decoded_request['loa']['payment_category_c']));
                }
                if (isset($decoded_request['loa']['paymentterms_c'])) {
                    $loa['Loa']['loaPaymentTermId'] = intval(trim($decoded_request['loa']['paymentterms_c']));
                }
                if (isset($decoded_request['loa']['term_c'])) {
                    $loa['Loa']['loaInstallmentTypeId'] = intval(trim($decoded_request['loa']['term_c']));
                }

                $loa['recipients'] = array(
                    $decoded_request['assigned_user']['email'] => $decoded_request['assigned_user']['user_name'],
                    $decoded_request['current_user']['email'] => $decoded_request['current_user']['user_name']
                );

                if ($loa['recipients'][0] == $loa['recipients'][1]){
                    //if both recipients are the same.
                    unset($loa['recipients'][1]);
                }

                $requiredFields = array(
                    $this->data['LoaDocument']['sugarLoaId'],
                );

                foreach ($requiredFields as $key => $field) {
                    if (!isset($field)) {
                        $errors[] = "Required fields missing";
                    }
                }
                $loa['Loa']['moneyBackGuarantee'] = '';
            } else {
                //non sugar
                $this->layout = 'ajax';
                $this->data['LoaDocument']['generatedBy'] = $this->user['LdapUser']['username'];
                $this->data['LoaDocument']['loaDocumentSourceId'] = 1;
                $loa['Loa']['notes'] = nl2br($loa['Loa']['notes']);
                $loa = $this->Loa->read(null, $this->data['LoaDocument']['loaId']);

                $this->data['LoaDocument']['companyName'] = $loa['Client']['name'];

                //deconstruct DocDate used in PDF from cake array
                $this->data['LoaDocument']['docDate'] = $this->LoaDocument->deconstruct(
                    'docDate',
                    $this->data['LoaDocument']['docDate']
                );
            }
            $nameParts = split_full_name($this->data['LoaDocument']['contactName']);
            $this->data['LoaDocument']['nameParts'] = $nameParts;

            if(!empty($this->data['LoaDocument']['contactPrefix'])){

                $loa['salutation'] = $this->data['LoaDocument']['contactPrefix'].' '.$nameParts['lname'];
            }else{
                $loa['salutation'] = $nameParts['fname'];
            }
            if(!empty($loa['Loa']['numEmailInclusions'])){
                $loa['Loa']['numEmailInclusionsWords'] = convert_number_to_words(intval($loa['Loa']['numEmailInclusions']));
            }
            //handle special characters for PDF.
            $loa['Client']['name'] = htmlentities($loa['Client']['name']);
            /*var_dump($loa['salutation']);
            die();
            */
            //$client = $this->Client->findByClientId($this->data['LoaDocument']['clientId']);
            $doc = $this->data;

            $checkboxValuesSelectedArr = array();
            $strCheckBox = null;
            if (isset($loa['Loa']['checkboxes'])) {
                $checkboxValuesSelectedArr = explode(",", $loa['Loa']['checkboxes']);
                if (!empty($checkboxValuesSelectedArr)) {
                    foreach ($checkboxValuesSelectedArr as $k => $val) {
                        $strCheckBox .= '<li>' . $this->LoaDocument->includeText($val) . '</li>';
                    }
                }
            }
            //$this->LoaDocument->includeTextHowItWorks($membershipTypeId, $paymentTermId, $installmentTypeId = null, $hotelName,$percentage= null)
            $loa['howText'] = $this->LoaDocument->includeTextHowItWorks(
                $loa['Loa']['loaMembershipTypeId'],
                $loa['Loa']['loaPaymentTermId'],
                $loa['Loa']['loaInstallmentTypeId'],
                $loa['Client']['name'],
                $loa['Loa']['revenueSplitPercentage']
            );
            //die(var_dump($loa['howText']));

            $loa = $doc + $loa + $checkboxValuesSelectedArr;

            $view = new View($this, false);
            $docContent = $view->element(
                'loa_pdf',
                array(
                    "loa" => $loa,
                )
            );
            $this->data['LoaDocument']['content'] = $docContent;

            if ($this->LoaDocument->save($this->data['LoaDocument']) && empty($errors)) {
                $prefix = 'http://';

                $hostName = 'toolbox.luxurylink.com';
                if ($_SERVER['ENV'] == 'development' || ISSTAGE == true){
                    $hostName = $_SERVER['HTTP_HOST'];
                }
                $docId = $this->LoaDocument->getLastInsertId();

                if ('json' !== $mode) {
                    $downloadUrl = $prefix . $hostName . $this->webroot . $this->params['controller'] . '/download/' . $this->data['LoaDocument']['loaId'] . '/' . $docId;
                    $result = 'Document Generated! Please download Here.<br />' . '<a href="' . $downloadUrl . '" target="_blank">' . $downloadUrl . '</a>';
                    $this->Session->setFlash(__($result, true), 'default', array(), 'success');
                } else {
                    //sugar success
                    $downloadUrl = $prefix . $hostName . $this->webroot . $this->params['controller'] . '/download/' . $this->data['LoaDocument']['sugarLoaId'] . '/' . $docId;
                    $localDownloadUrl = $prefix . $_SERVER['SERVER_ADDR'] . $this->webroot . $this->params['controller'] . '/download/' . $this->data['LoaDocument']['sugarLoaId'] . '/' . $docId;
                    $result = array(
                        'response' => 1,
                        'message' => array(
                            'pdf' => $downloadUrl,
                            'pdfLocal' => $localDownloadUrl,
                            'docId' => $docId,
                            'revision' => ''
                        ),
                        'data' => $loa
                    );
                    //log invalid request
                    $this->ConnectorLog->setParam('status', 1);
                    $this->ConnectorLog->setParam('response',json_encode($result));
                }
            } else {
                //did not save
                if ('json' !== $mode) {
                    //not from sugar
                    $this->Session->setFlash(
                        __(
                            'Loa Document not saved. Please correct the following errors.<br />' . implode(
                                '<br />',
                                $this->LoaDocument->validationErrors
                            ),
                            true
                        ),
                        'default',
                        array(),
                        'error'
                    );
                } else {
                    //sugar error
                    $errors = array_merge($this->LoaDocument->validationErrors, $errors);
                    $result = array('response' => 0, 'message' => array('errors' => $errors, 'data' => $loa));

                    //log invalid request
                    $this->ConnectorLog->setParam('status', 0);
                    //show error messages in connector log for troubleshooting
                    $this->ConnectorLog->setParam('errorMsg', print_r($errors, true));
                }
            }
            $this->ConnectorLog->execute();
        }
        $this->set('mode', $mode);
        //$this->set('client', $client);
        $this->set('results', $result);
    }
}
