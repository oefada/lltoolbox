<?php
class LoaMembershipType extends AppModel
{

    var $name = 'LoaMembershipType';
    var $useTable = 'loaMembershipType';
    var $primaryKey = 'loaMembershipTypeId';
    var $displayField = 'loaMembershipTypeName';
    var $order = 'loaMembershipTypeName';


    public function getMemberShipTypeIDbyName($loaMembershipTypeName = null)
    {
        if (!isset($loaMembershipTypeName)) {
            return null;
        }
        $data = $this->findByloaMembershipTypeName($loaMembershipTypeName);

        if (empty($data)) {
            return null;
        }
        return $data['LoaMembershipType']['loaMembershipTypeId'];
    }
}
