<?php
/**
 * User: oefada
 * Date: 10/16/13
 * Time: 4:49 PM
 */

class PackageTypeRel extends AppModel
{

    public $name = 'PackageTypeRel';
    public $useTable = 'packageTypeRel';
    public $primaryKey = 'packageTypeRelid';

    //public $hasOne = array('PackageType' => array('foreignKey' => 'packageTypeId'));

    public function packageTypeExistsForPackage($packageId, $packageTypeId)
    {
        if (!isset($packageId, $packageTypeId)) {
            //if not set return false
            return false;
        }
        $results = $this->find(
            'first',
            array(
                'conditions' => array(
                    'PackageTypeRel.packageTypeId' => $packageTypeId,
                    'PackageTypeRel.packageId' => $packageId
                )
            )
        );
        if (!empty($results)){
            return true;
        }
        return false;
    }
}

?>