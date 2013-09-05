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
    /**!IImportant for sugar LOA integration
     * @param string $loaInstallmentType
     * @return int   id
     */
    public function getInstallmentTypeIDbyName($loaInstallmentType=null)
    {
        if (!isset($loaInstallmentType)){
            return null;
        }
        $data = $this->findByname($loaInstallmentType);
        if (empty($data)){
            return null;
        }
        return $data['LoaInstallmentType']['loaInstallmentTypeId'];
    }

}