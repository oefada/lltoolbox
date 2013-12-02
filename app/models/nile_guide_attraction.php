<?php
class NileGuideAttraction extends AppModel
{
    var $name = 'NileGuideAttraction';
    var $useDbConfig = 'luxurylink';
    var $useTable = 'nileGuideAttractions';
    var $primaryKey = 'id';
    var $order = 'title';

    public function import($data)
    {
        die("NileGuideAttaction import\n\n" . print_r($data, true));
    }

}
