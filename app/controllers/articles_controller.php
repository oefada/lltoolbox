<?php
class ArticlesController extends AppController {

	var $name = 'Articles';
	var $helpers = array('Html', 'Form');
	var $uses = array('Article', 'ArticleRel', 'LandingPage');

	function index() {
		$this->Article->recursive = 0;
		$this->set('articles', $this->paginate());
		$primaryStyles = $this->LandingPage->find('list', array('order' => 'landingPageName'));
		$this->set('primaryStyleIds', $primaryStyles);
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Article.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('article', $this->Article->read(null, $id));
		$primaryStyles = $this->LandingPage->find('list', array('order' => 'landingPageName'));
		$this->set('primaryStyleIds', $primaryStyles);
	}

	function add() {
		if (!empty($this->data)) {
			$this->data['Article']['articleSeoName'] = $this->convertToSeoName($this->data['Article']['articleTitle']);
			$this->data['Article']['articleUrlDisplay'] = $this->getArticleUrlDisplay($this->data['Article']['primaryStyleId']);
			$this->Article->create();
			if ($this->Article->save($this->data)) {

				$articleRel = array();
				$articleRel['articleId'] = $this->Article->id;
				$articleRel['refId'] = $this->data['Article']['primaryStyleId'];
				$pstyle = $this->LandingPage->read(null, $this->data['Article']['primaryStyleId']);
				if ($pstyle['LandingPage']['landingPageTypeId'] == 1) {
					$articleRel['articleRelTypeId'] = 1;		
				} 
				if ($pstyle['LandingPage']['landingPageTypeId'] == 2) {
					$articleRel['articleRelTypeId'] = 2;		
				} 
				$this->ArticleRel->create();
				$this->ArticleRel->save($articleRel);

				$this->Session->setFlash(__('The Article has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Article could not be saved. Please, try again.', true));
			}
		}
		$primaryStyles = $this->LandingPage->find('list', array('order' => 'landingPageName'));
		$this->set('primaryStyleIds', $primaryStyles);
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Article', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			$this->data['Article']['articleUrlDisplay'] = $this->getArticleUrlDisplay($this->data['Article']['primaryStyleId']);
			$this->data['Article']['articleSeoName'] = $this->convertToSeoName($this->data['Article']['articleTitle']);
			if ($this->Article->save($this->data)) {
				$this->Session->setFlash(__('The Article has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Article could not be saved. Please, try again.', true));
			}
		}
		$primaryStyles = $this->LandingPage->find('list', array('order' => 'landingPageName'));
		$this->set('primaryStyleIds', $primaryStyles);
		if (empty($this->data)) {
			$this->data = $this->Article->read(null, $id);
		}
	}

	function edit_rel($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Article', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Article->save($this->data)) {
				$this->Session->setFlash(__('The Article has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Article could not be saved. Please, try again.', true));
			}
		}
		$primaryStyles = $this->LandingPage->find('list', array('order' => 'landingPageName'));
		$this->set('primaryStyleIds', $primaryStyles);
		if (empty($this->data)) {
			$this->data = $this->Article->read(null, $id);

			if (!empty($this->data['ArticleRel'])) {
				foreach ($this->data['ArticleRel'] as $key => $v) {
					if (($v['articleRelTypeId'] == 4) && ($v['refId'])) {  // for clientId ref
						$client_name = $this->Article->query('SELECT name FROM client WHERE clientId = ' . $v['refId']);
						$this->data['ArticleRel'][$key]['refName'] = $client_name[0]['client']['name'];
					} else {
						$this->data['ArticleRel'][$key]['refName'] = $primaryStyles[$v['refId']];
					}
				}
			}

		}

	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Article', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Article->del($id)) {
			$this->Session->setFlash(__('Article deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}
	
	function getArticleUrlDisplay($primaryStyleId) {
		$this->LandingPage->recursive = -1;
		$landingPage = $this->LandingPage->read(null, $primaryStyleId);
		$styleName = $this->convertToSeoName($landingPage['LandingPage']['landingPageName']);
		switch ($landingPage['LandingPage']['landingPageTypeId']) {
			case 1:
				$articleUrlDisplay = "/destinations/$styleName/a/";
				break;
			case 2: 
				$articleUrlDisplay = "/styles-interests/$styleName/a/";
				break;
		}
		return $articleUrlDisplay;
	}

	function convertToSeoName($str) {
	    $str = strtolower(html_entity_decode($str, ENT_QUOTES, "ISO-8859-1"));  // convert everything to lower string
	    $search_accent = explode(",","ç,æ,~\,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u,ñ");
	    $replace_accent = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u,n");
	    $search_accent[] = '&';
	    $replace_accent[] = ' and ';
	    $str = str_replace($search_accent, $replace_accent, $str);
	    $str = preg_replace("/<([^<>]*)>/", ' ', $str);                     // remove html tags
	    $str_array = preg_split("/[^a-zA-Z0-9]+/", $str);                   // remove non-alphanumeric
	    $count_a = count($str_array);
	    if ($count_a) {
	        if ($str_array[0] == 'the') {
	            array_shift($str_array);
	        }
	        if (isset($str_array[($count_a - 1)]) && (($str_array[($count_a - 1)] == 'the') || !$str_array[($count_a - 1)])) {
	            array_pop($str_array);
	        }
	        for ($i=0; $i<$count_a; $i++) {
	            if ($str_array[$i]=='s' && strlen($str_array[($i - 1)])>1) {
	                $str_array[($i - 1)] = $str_array[($i - 1)] . 's';
	                unset($str_array[$i]);
	            } elseif ($str_array[$i]=='' || !$str_array[$i]) {
	                unset($str_array[$i]);
	            }
	        }
	        return (substr(implode('-', $str_array), 0, 499));
	    }else {
	        return '';
	    }
	}

}
?>
