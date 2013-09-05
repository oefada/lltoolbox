<?php
class LoaPaymentTerm extends AppModel
{
    public $name = 'LoaPaymentTerm';
    public $displayField = 'description';


    public function getPaymentTermIDbyName($loaPaymentTerm = null)
    {
        if (!isset($loaPaymentTerm)){
            return null;
        }
        $data = $this->findBydescription($loaPaymentTerm);
        if (empty($data)){
            return null;
        }
        return $data['LoaPaymentTerm']['loaPaymentTermId'];
    }
}
