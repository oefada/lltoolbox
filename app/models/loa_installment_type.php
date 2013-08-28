<?php
class LoaInstallmentType extends AppModel
{

    var $name = 'LoaInstallmentType';
//    var $useTable = 'loaInstallmentType';
//    var $primaryKey = 'loaInstallmentTypeId';
    var $displayField = 'name';
    var $order = 'loaInstallmentTypeId';


    public function getInstallmentTypeById($id)
    {
        if (!isset($id)){
            return null;
        }
        $data= $this->findByloaInstallmentTypeId($id);
        return $data['LoaInstallmentType']['name'];
    }

}