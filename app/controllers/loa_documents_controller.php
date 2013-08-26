<?php
class LoaDocumentsController extends AppController
{
    public $name = 'LoaDocuments';
    public $helpers = array('Html', 'Form', 'Ajax', 'Javascript', 'Time');
    public $uses = array('LoaDocument', 'Client', 'Loa');

    public function beforeFilter()
    {
        $this->LdapAuth->allow('*');
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
     * @param int $loaId
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
            "LoaDocument.loaId" => $loaId,
            "LoaDocument.loaDocumentId" => $loaDocumentId,
            "LoaDocument.active" => 1
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

    public function save_document()
    {
        $mode = null;
        $this->layout = 'ajax';
        if (isset($this->params['named']['jsonp'])) {
            $mode = 'jsonp';
        }
        if (isset($mode)) {
            //$this->layout = false;
            $this->set('inStr', $this->params['named']['jsonp']);
        }
        if (isset($sm_request)) {
            // JSON decoded the request into an assoc. array
            $decoded_request = json_decode($sm_request, true);
            $this->set('decoded_request', $decoded_request);
        }
        if (!empty($this->data)) {
            $loa = $this->Loa->read(null, $this->data['LoaDocument']['loaId']);
            $client = $this->Client->findByClientId($this->data['LoaDocument']['clientId']);
            $doc = $this->data;

            $checkboxValuesSelectedArr = array();
            $strCheckBox = null;
            if (isset($loa['Loa']['checkboxes'])){
                $checkboxValuesSelectedArr = explode(",",$loa['Loa']['checkboxes']);
                if (!empty($checkboxValuesSelectedArr)){
                    foreach ($checkboxValuesSelectedArr as $k=>$val)
                        $strCheckBox .= '<li>'.$this->LoaDocument->includeText($val).'</li>';
                }
            }
            $loa = $doc + $loa + $checkboxValuesSelectedArr;
            $view = new View($this, false);
            $docContent = $view->element(
                'loa_pdf',
                array(
                    "loa" => $loa,
                )
            );
            $this->data['LoaDocument']['content'] = $docContent;
            if ($this->LoaDocument->save($this->data['LoaDocument'])) {
                $prefix = 'http://';
                $hostName = $_SERVER['HTTP_HOST'];
                $downloadURl = $prefix . $hostName . $this->webroot . $this->params['controller'] . '/download/' . $this->data['LoaDocument']['loaId'] . '/' . $this->LoaDocument->getLastInsertId(
                    );
                $result = 'Document Generated! Please download Here.<br />' .
                    '<a href="' . $downloadURl . '" target="_blank">' . $downloadURl . '</a>';
                $this->Session->setFlash(__($result, true), 'default', array(), 'success');
            } else{

                $this->Session->setFlash(__('Loa Document not saved. Please correct the following errors.<br />'.implode('<br />', $this->LoaDocument->validationErrors), true), 'default', array(), 'error');
            }
        }
        $this->set('mode', $mode);
        $this->set('client', $client);
        $this->set('strCheckBox', $strCheckBox);
    }
}
