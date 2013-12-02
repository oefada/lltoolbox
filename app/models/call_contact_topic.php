<?php
/**
 * User: oefada
 * Date: 9/30/13
 * Time: 4:26 PM
 */
class CallContactTopic extends AppModel
{
    public $name = 'CallContactTopic';
    public $useTable = 'callContactTopics';
    public $primaryKey = 'callContactTopicId';
    public $displayField = 'callContactTopicDescription';
    public $order = 'sortOrder ASC';

    public function getActiveCallContactTopics()
    {
        $preTicket = $this->find('list',
            array(
                'conditions' =>
                array(
                    'CallContactTopic.Deleted !=' => '1','callContactTopicGroupId' => '1'
                )
            )
        );
        $postTicket = $this->find('list',
            array(
                'conditions' =>
                array('CallContactTopic.Deleted !=' => '1', 'callContactTopicGroupId' => '2')
            )
        );
        $results = array('Pre-Ticket' => $preTicket, 'Post-Ticket' => $postTicket);
        return $results;
    }
}


