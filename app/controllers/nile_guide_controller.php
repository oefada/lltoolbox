<?php
class NileGuideController extends AppController
{

    var $name = 'NileGuide';
    var $uses = array(
        'NileGuideTrip',
        'NileGuideTripItinerary',
        'NileGuideDestinationRel',
        'NileGuideAttraction',
    );
    var $helpers = array(
        'Html',
        'Form'
    );

    function index()
    {
        if (isset($this->params['url']['data']['title']) && strlen($this->params['url']['data']['title']) >= 3) {
            $titleSearch = '%' . $this->params['url']['data']['title'] . '%';
            $results = $this->NileGuideAttraction->find(
                'all',
                array('conditions' => array('NileGuideAttraction.title LIKE' => $titleSearch))
            );
            $this->set('attractions', $results);
        }
    }

    function attraction($id)
    {
        if ($id) {
            if (isset($this->data['NileGuideAttraction'])) {
                if (isset($this->data['NileGuideAttraction']['id']) && isset($this->data['NileGuideAttraction']['publish'])) {
                    $this->NileGuideAttraction->create();
                    $this->NileGuideAttraction->save($this->data);
                }
            }
            $this->set(
                'attraction',
                $this->NileGuideAttraction->find('first', array('conditions' => array('ngId' => $id)))
            );
        } else {
            $this->redirect(array('action' => 'index'));
        }
    }

    function url()
    {
        $url = '';
        if (isset($_GET['q'])) {
            $url = $_GET['q'];
            $url = basename($url);
            $url = preg_replace('/-.*$/', '', $url);
        }
        if (is_numeric($url) && $url > 0) {
            $this->redirect(
                array(
                    'action' => 'attraction',
                    $url,
                )
            );
        } else {
            $this->Session->setFlash('Could not recognize Nile Guide item.');
            $this->redirect(array('action' => 'index'));
        }
        die('<pre>' . htmlentities(print_r($url, true)));

    }

}
