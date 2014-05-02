<?php
class PromoCodesController extends AppController
{
    public $name = 'PromoCodes';
    public $helpers = array('Html', 'Form');

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     */
    public function index()
    {
        $this->PromoCode->recursive = 2;
        $this->paginate['order'] = array('promoCodeId' => 'DESC');
        $this->paginate['limit'] = 100;
        $this->set('promoCodes', $this->paginate());
    }

    /**
     * @param $promoCode
     */
    public function ajax_valid_promo($promoCode)
    {
        $query = "SELECT promoCode.promoCodeId,giftCertBalance.balance,giftCertBalance.userId,promoCodeRel.promoCodeRelId,promo.amountOff,promo.percentOff FROM promoCode
            LEFT JOIN giftCertBalance USING (promoCodeId)
            LEFT JOIN promoCodeRel USING (promoCodeId)
            LEFT JOIN promo USING (promoId)
            WHERE promoCode = '" . $promoCode . "'
            AND IF(promo.promoId IS NOT NULL,promo.endDate > NOW(),1) = 1
            ORDER BY giftCertBalance.giftCertBalanceId DESC LIMIT 1";

        $promo = $this->PromoCode->query($query);
        echo json_encode($promo[0]);
        exit;
    }

    /**
     * @param null $id
     */
    public function view($id = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid PromoCode.', true));
            $this->redirect(array('action' => 'index'));
        }

        $this->set('promoCode', $this->PromoCode->read(null, $id));
    }

    /**
     * @param null $id
     */
    public function add($id = null)
    {

        if (!$this->PromoCode->Promo->hasEditAccess($this->LdapAuth->user())) {
            $this->Session->setFlash(__('You do not have permission to edit promos.', true));
            $this->redirect(array('controller' => 'promos', 'action' => 'index'));
        }

        if (!empty($this->data)) {
            if (empty($this->data['PromoCode']['promoCode']) && $this->data['totalCode'] && $this->data['prefix']) {
                $results = $this->PromoCode->query("SELECT GROUP_CONCAT(promoCode) AS promoCodes FROM promoCode");
                $promo_codes = explode(',', $results[0][0]['promoCodes']);
                for ($x = 0; $x < $this->data['totalCode']; $x++) {
                    $promo_code = $this->PromoCode->__generateCode(strlen($this->data['totalCode']));
                    if (in_array($promo_code, $promo_codes)) { // TODO: GOTTA CHECK DB AS WELL
                        $x--;
                    } else {
                        $promo_codes[] = $promo_code;
                        $this->data['PromoCode']['promoCode'] = $this->data['prefix'] . $promo_code;
                        $this->PromoCode->create();
                        $this->PromoCode->save($this->data);
                    }
                }

                $this->Session->setFlash(__('The Promo Codes have been saved', true));
                $this->redirect(array('controller' => 'promo_code_rels', 'action' => 'index', $this->data['Promo']['Promo']));

            } elseif (!empty($this->data['PromoCode']['promoCode'])) {
                $this->PromoCode->create();
                if ($this->PromoCode->save($this->data)) {
                    $this->Session->setFlash(__('The Promo Code has been saved', true));
                    $this->redirect(array('controller' => 'promo_code_rels', 'action' => 'index', $this->data['Promo']['Promo']));
                } else {
                    $this->Session->setFlash(__('The PromoCode could not be saved. Please, try again.', true));
                }
            }
        } else {
            if ($id) {
                $this->data['Promo']['Promo'] = $id;
            }
        }

        $promos = $this->PromoCode->Promo->find('list', array('order' => array('Promo.promoName')));
        $this->set('promoIds', $promos);
        $this->set(compact('promos'));
    }

    /**
     * @param null $id
     */
    public function edit($id = null)
    {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid PromoCode', true));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->PromoCode->save($this->data)) {
                $this->Session->setFlash(__('The PromoCode has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The PromoCode could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->PromoCode->read(null, $id);
        }
        $promos = $this->PromoCode->Promo->find('list');
        $this->set(compact('promos'));
    }

    /**
     * @param null $id
     */
    public function delete($id = null)
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for PromoCode', true));
            $this->redirect(array('action' => 'index'));
        }
        if ($this->PromoCode->del($id)) {
            $this->Session->setFlash(__('PromoCode deleted', true));
            $this->redirect(array('action' => 'index'));
        }
    }
}
