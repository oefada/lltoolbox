<?php
class ArticleRelsController extends AppController {

	var $name = 'ArticleRels';
	var $helpers = array('Html', 'Form');

	function add($articleRelTypeId = null, $articleId = null) {
		if (!empty($this->data)) {
			$this->ArticleRel->create();
			if ($this->ArticleRel->save($this->data)) {
				$this->Session->setFlash(__('The ArticleRel has been saved', true));
				$this->redirect(array('controller' => 'articles', 'action'=>'edit', 'id' => $articleId));
			} else {
				$this->Session->setFlash(__('The ArticleRel could not be saved. Please, try again.', true));
			}
		}
		$articleRelTypes = $this->ArticleRel->ArticleRelType->find('list');
		$this->set('articleRelTypeIds', $articleRelTypes);
		$this->set('articleRelTypeId', $articleRelTypeId);
		$this->set('articleId', $articleId);
		$this->data['ArticleRel']['articleRelTypeId'] = $articleRelTypeId;
		$this->data['ArticleRel']['articleId'] = $articleId;
	}

	function edit($id = null, $articleId = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid ArticleRel', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->ArticleRel->save($this->data)) {
				$this->Session->setFlash(__('The ArticleRel has been saved', true));
				$this->redirect(array('controller' => 'articles', 'action'=>'edit_rel', 'id' => $articleId));
			} else {
				$this->Session->setFlash(__('The ArticleRel could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->ArticleRel->read(null, $id);
		}
		$articleRelTypes = $this->ArticleRel->ArticleRelType->find('list');
		$this->set('articleRelTypeIds', $articleRelTypes);
		$this->set('relId', $id);
		$this->set('articleId', $articleId);
	}

	function delete($id = null, $articleId = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for ArticleRel', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->ArticleRel->del($id)) {
			$this->Session->setFlash(__('ArticleRel deleted', true));
			$this->redirect(array('controller' => 'articles', 'action'=>'edit_rel', 'id' => $articleId));
		}
	}

}
?>
