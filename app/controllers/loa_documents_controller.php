<?php
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
        'ConnectorLog'
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
                $this->data['LoaDocument']['contactName'] = $decoded_request['client']['main_contact_name'];
                $this->data['LoaDocument']['contactTitle'] = '';
                $this->data['LoaDocument']['docDate'] = $decoded_request['loa']['date_modified'];
                $this->data['LoaDocument']['generatedBy'] = $decoded_request['current_user']['user_name'];
                //important for giving docs right name
                $this->data['LoaDocument']['companyName'] = $sugarClientName;

                //register Loa Fields
                $loa['Client']['companyName'] = $sugarClientName;
                $loa['Client']['segment'] = $decoded_request['client']['segment'];

                //fees and dates
                $loa['Loa']['membershipFee'] = $decoded_request['loa']['agreement_fee_c'];
                $loa['Loa']['auctionCommissionPerc'] = $decoded_request['loa']['commission_auction_c'];
                $loa['Loa']['buynowCommissionPerc'] = $decoded_request['loa']['commission_buynow_c'];
                $loa['Loa']['startDate'] = $decoded_request['loa']['effective_date_c'];
                $loa['Loa']['endDate'] = $decoded_request['loa']['expiration_date_c'];

                $loa['Loa']['notes'] = $decoded_request['loa']['special_instructions_c'];

                //fields to translate
                if (isset($decoded_request['loa']['payment_category_c'])) {
                    /**$loa['Loa']['loaMembershipTypeId'] = $this->LoaMembershipType->getMemberShipTypeIDbyName(
                        $decoded_request['loa']['payment_category_c']
                    );*/
                    $loa['Loa']['loaMembershipTypeId'] = $decoded_request['loa']['payment_category_c'];
                }
                if (isset($decoded_request['loa']['paymentterms_c'])) {
                    /**$loa['Loa']['loaPaymentTermId'] = $this->LoaPaymentTerm->getPaymentTermIDbyName(
                        $decoded_request['loa']['paymentterms_c']
                    );**/
                    $loa['Loa']['loaPaymentTermId'] = $decoded_request['loa']['paymentterms_c'];
                }
                if (isset($decoded_request['loa']['term_c'])) {
                   /** $loa['Loa']['loaInstallmentTypeId'] = $this->LoaInstallmentType->getInstallmentTypeIDbyName(
                        $decoded_request['loa']['term_c']
                    );**/
                    $loa['Loa']['loaInstallmentTypeId'] = $decoded_request['loa']['term_c'];
                }

                $loa['recipients'][] = $decoded_request['assigned_user']['email'];
                $loa['recipients'][] = $decoded_request['current_user']['email'];

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
                $loa = $this->Loa->read(null, $this->data['LoaDocument']['loaId']);
                //deconstruct DocDate used in PDF from cake array
                $this->data['LoaDocument']['docDate'] = $this->LoaDocument->deconstruct(
                    'docDate',
                    $this->data['LoaDocument']['docDate']
                );
            }
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
            $loa['howText'] = $this->LoaDocument->includeTextHowItWorks(
                $loa['Loa']['loaMembershipTypeId'],
                $loa['Loa']['loaPaymentTermId'],
                $loa['Loa']['loaInstallmentTypeId'],
                $loa['Client']['companyName']
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
                $hostName = $_SERVER['HTTP_HOST'];
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
