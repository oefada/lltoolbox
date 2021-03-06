<?php
class CreditTrackingsController extends AppController
{
    public $name = 'CreditTrackings';
    public $helpers = array('Html', 'Form');
    public $canSave = false;

    /**
     * @var User $userModel
     */
    public $userModel;


    public function __construct()
    {
        parent::__construct();
        APP::import('Model', 'User');
        $this->userModel = new User();
    }

    /**
     *
     */
    public function beforeFilter()
    {
        parent::beforeFilter();

        $currentUser = $this->LdapAuth->user();
        if (
            in_array('Accounting', $currentUser['LdapUser']['groups'])
            || in_array('Geeks', $currentUser['LdapUser']['groups'])
            || in_array('cof', $currentUser['LdapUser']['groups'])
        ) {
            $this->canSave = true;
        }

        $this->set('canSave', $this->canSave);
    }

    /**
     *
     */
    public function index()
    {
        $this->UserSiteExtended->primaryKey = 'userId';
        $conditions = array();

        if (isset($this->params['named']['query'])) {
            $query = $this->params['named']['query'];
            $conditions = array(
                'OR' => array(
                    'CreditTracking.userId LIKE' => '%' . $query . '%',
                    'CreditTracking.userId' => $query,
                    'UserSiteExtended.username LIKE' => '%' . $query . '%',
                ),
            );

            $this->set('query', $query);
        }
        $this->paginate = array(
            'fields' => array(
                'creditTrackingId',
                'balance',
                'userId',
                'datetime'
            ),
            'conditions' => $conditions,
            'limit' => 50,
            'order' => array(
                'creditTrackingId' => 'desc',
            ),
            'contain' => array(
                'UserSiteExtended' => array(
                    'fields' => array(
                        'UserSiteExtended.userId',
                        'UserSiteExtended.username'
                    ),
                ),
                'User' => array(
                    'fields' => array(
                        'User.userId',
                        'User.email'
                    ),
                ),
            ),
        );

        $this->set('creditTrackings', $this->paginate());
    }

    public function view($id = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid CreditTracking.', true));
            $this->redirect(array('action' => 'index'));
        }
        // $this->CreditTracking->primaryKey = 'userId';
        $trackings = $this->CreditTracking->find(
            'all',
            array(
                'conditions' => array('CreditTracking.userId' => $id),
                'order' => array('CreditTracking.creditTrackingId')
            )
        );
        $this->set('creditTrackings', $trackings);
    }

    /**
     *
     */
    public function add()
    {
        $this->canSave();
        if (!empty($this->data)) {
            if ($this->userModel->isInternational($this->data['CreditTracking']['userId']) === false) {
                if ($this->CreditTracking->saveAll($this->data)) {
                    $this->Session->setFlash(__('The CreditTracking has been saved', true));
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(__('The CreditTracking could not be saved. Please, try again.', true));
                }
            } else {
                $this->Session->setFlash(__('A Credit on File cannot be added for non-US users.', true));
            }
        }
        $creditTrackingTypes = $this->CreditTracking->CreditTrackingType->find('list');
        $this->set('creditTrackingTypeIds', $creditTrackingTypes);
        $this->set(compact('creditTrackingTypes'));
    }

    public function edit($id = null)
    {
        $this->canSave();
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid CreditTracking', true));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->CreditTracking->save($this->data)) {
                $this->Session->setFlash(__('The CreditTracking has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The CreditTracking could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->CreditTracking->read(null, $id);
        }
        $creditTrackingTypes = $this->CreditTracking->CreditTrackingType->find('list');
        $this->set(compact('creditTrackingTypes'));
    }

    public function delete($id = null)
    {
        $this->canSave();

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for CreditTracking', true));
            $this->redirect(array('action' => 'index'));
        }
        if ($this->CreditTracking->del($id)) {
            $this->Session->setFlash(__('Entry deleted', true));

            $userId = "";
            $action = "index";

            if (isset($this->params['named']['userId'])) {
                $action = "view";
                $userId = $this->params['named']['userId'];
            }

            $this->redirect(array('action' => $action, $userId));
        }
    }

    public function search()
    {
        $this->redirect(array('action' => 'index', 'query' => $this->params['url']['query']));
    }

    public function canSave()
    {
        if ($this->canSave == false) {
            $this->Session->setFlash('You are not authorized to view this page');
            $this->redirect("/credit_trackings/");
        }
    }
}
