<?php
class RoomGradesController extends AppController {

   var $name = 'RoomGrades';
   
   function beforeFilter() {
	  parent::beforeFilter();
	  $this->set('currentTab', 'property');
	  if (isset($this->params['clientId']) && empty($clientId)) {
		 $this->RoomGrade->clientId = $this->params['clientId'];
	  } else {
		 $this->params['clientId'] = $this->RoomGrade->clientId;
	  }
	  $this->RoomGrade->client = $this->RoomGrade->Client->findByClientId($this->RoomGrade->clientId);
	  $this->set('clientId', $this->RoomGrade->clientId);
	  $this->set('client', $this->RoomGrade->client);
   }
   
   function index() {
        if (!empty($this->data)) {
            $this->data['RoomGrade']['clientId'] = $this->RoomGrade->clientId;
            if (!isset($this->data['RoomGrade']['roomGradeId'])) {
                $this->data['RoomGrade']['roomGradeId'] = null;
            }
			
			// check and make sure link has http://
			if (isset($this->data['RoomGrade']['roomLink']) && !empty($this->data['RoomGrade']['roomLink'])) {
				$link = $this->data['RoomGrade']['roomLink'];
				if (strtolower(substr($link, 0, 7)) != 'http://') {
					$link = 'http://' . $link;
					$this->data['RoomGrade']['roomLink'] = $link;
				}
			}
			
			
            if ($this->RoomGrade->save($this->data)) {
                $this->Session->setFlash('Room Grade has been saved.');
            }
            else {
                $this->Session->setFlash('Room Grade could not be saved.');
            }
        }
        $this->RoomGrade->recursive = 2;
        $roomGrades = $this->RoomGrade->find('all', array('conditions' => array('RoomGrade.clientId' => $this->RoomGrade->clientId),
                                                           'order' => array('RoomGrade.roomGradeName'))
                                            );
        $this->set('roomGrades', $roomGrades);
   }
   
   function edit($clientId, $roomGradeId) {
        if (!empty($this->data)) {
			print_r($this->data);
			// check and make sure link has http://
			if (isset($this->data['RoomGrade']['roomLink']) && !empty($this->data['RoomGrade']['roomLink'])) {
				$link = $this->data['RoomGrade']['roomLink'];
				if (strtolower(substr($link, 0, 7)) != 'http://') {
					$link = 'http://' . $link;
					$this->data['RoomGrade']['roomLink'] = $link;
				}
			}
			
            if ($this->RoomGrade->save($this->data)) {
                $this->Session->setFlash('Room Grade has been saved.');
            }
            else {
                $this->Session->setFlash('Room Grade could not be saved.');
            }
        }
        $this->recursive = -1;
        $roomGrade = $this->RoomGrade->findByRoomGradeId($roomGradeId);
        $this->set('roomGrade', $roomGrade);
   }
   
   function delete($clientId, $roomGradeId) {
        if ($this->RoomGrade->delete($roomGradeId)) {
            $this->Session->setFlash('Room Grade has been deleted');
        }
        else {
            $this->Session->setFlash('Room Grade could not be deleted');
        }
        $this->redirect($this->referer());
   }
   
}
?>