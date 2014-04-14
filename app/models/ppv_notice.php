<?php
class PpvNotice extends AppModel
{
    public $name = 'PpvNotice';
    public $useTable = 'ppvNotice';
    public $primaryKey = 'ppvNoticeId';
    public $actsAs = array('Logable');

    public $belongsTo = array(
        'PpvNoticeType' => array(
            'foreignKey' => 'ppvNoticeTypeId'
        )
    );
}
