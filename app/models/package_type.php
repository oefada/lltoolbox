<?php


class PackageType extends AppModel
{

    public $name = 'PackageType';
    public $useTable = 'packageType';
    public $primaryKey = 'packageTypeId';

  /**  public function getCheckBoxArr()
    {
        $results = $this->find('all');
        if (!empty($results))
        {
            return $results;
        }
        return false;
    } **/

}

?>