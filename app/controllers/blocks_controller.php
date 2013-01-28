<?php

$loadModules = array(
	'BlockMarkupModule',
	'BlockPageModule',
	'BlockDivModule',
);
foreach ($loadModules as $blockModuleName) {
	App::import('Vendor', $blockModuleName, array('file' => 'appshared' . DS . 'modules' . DS . $blockModuleName . DS . $blockModuleName . '.php'));
}

class BlocksController extends AppController
{
	var $name = 'Blocks';
	var $helpers = array('Html');
	var $uses = array(
		'BlockPage',
		'BlockRevision',
	);
	var $defaultTemplate = '[{"data": "Ticket 3481 | Luxury...", "attr": {"rel": "BlockPageModule"}, "state": "open", "metadata": {"meta_title": "Ticket 3481 | Luxury Link"}, "children": [{"data": "full", "attr": {"rel": "BlockLayoutModule"}, "state": "open", "metadata": {"class": "full"}, "children": [{"data": "BlockDivModule", "attr": {"rel": "BlockDivModule"}, "state": "open", "metadata": {}, "children": [{"data": "BlockPhotoModule", "attr": {"rel": "BlockPhotoModule"}, "state": "open", "metadata": {}, "children": [{"data": "Kittens!", "attr": {"rel": "BlockImageModule"}, "metadata": {"src": "http://placekitten.com/960/270", "linkHref": "http://www.youtube.com/watch?v=-efQuSlxgWY", "title": "Kittens!"}}]}]}]}, {"data": "content", "attr": {"rel": "BlockLayoutModule"}, "state": "open", "metadata": {"class": "content"}, "children": [{"data": "BlockDivModule", "attr": {"rel": "BlockDivModule"}, "state": "open", "metadata": {}, "children": [{"data": "BlockTabsModule", "attr": {"rel": "BlockTabsModule"}, "state": "open", "metadata": {}, "children": [{"data": "Awesome Properties", "attr": {"rel": "BlockTabModule"}, "state": "open", "metadata": {"title": "Awesome Properties"}, "children": [{"data": "BlockDivModule", "attr": {"rel": "BlockDivModule"}, "state": "open", "metadata": {}, "children": [{"data": "BlockClientDisplayModule", "attr": {"rel": "BlockClientDisplayModule"}, "metadata": {"clientIds": "8455\n3225"}}]}]}, {"data": "More Awesome Hotels!", "attr": {"rel": "BlockTabModule"}, "state": "open", "metadata": {"title": "More Awesome Hotels!", "linkBoxText": "Click Here", "linkBoxHref": "/vacation-ideas/ski-snow-resorts/deals?did=114"}, "children": [{"data": "BlockDivModule", "attr": {"rel": "BlockDivModule"}, "state": "open", "metadata": {}, "children": [{"data": "BlockClientDisplayModule", "attr": {"rel": "BlockClientDisplayModule"}, "metadata": {"clientIds": "11432,1205,11070"}}]}]}]}]}, {"data": "BlockDivModule", "attr": {"rel": "BlockDivModule"}, "state": "open", "metadata": {"class": "content-box"}, "children": [{"data": "Demo Page Heading", "attr": {"rel": "BlockHeaderModule"}, "metadata": {"content": "Demo Page Heading", "level": "1"}}, {"data": "Making new pages is ...", "attr": {"rel": "BlockParagraphModule"}, "metadata": {"content": "Making new pages is fun and easy!"}}, {"data": "BlockDivModule", "attr": {"rel": "BlockDivModule"}, "state": "open", "metadata": {"class": "link-box"}, "children": [{"data": "View All Vacation Ex...", "attr": {"rel": "BlockLinkModule"}, "metadata": {"content": "View All Vacation Experiences", "href": "/vacation-ideas/ski-snow-resorts/deals", "clicktrack": "clicktrack_blocks|vacation_ideas-ski_snow_resorts-best-view_all_vacation_experiences"}}]}]}, {"data": "NewsletterModuleCont...", "attr": {"rel": "BlockPrefabModule"}, "metadata": {"type": "NewsletterModuleContent"}}]}, {"data": "sidebar", "attr": {"rel": "BlockLayoutModule"}, "state": "open", "metadata": {"class": "sidebar"}, "children": [{"data": "BlockAdvertisingModule", "attr": {"rel": "BlockAdvertisingModule"}, "metadata": {}}, {"data": "FeaturedAuctionsModu...", "attr": {"rel": "BlockPrefabModule"}, "metadata": {"type": "FeaturedAuctionsModule"}}, {"data": "CommunityModule", "attr": {"rel": "BlockPrefabModule"}, "metadata": {"type": "CommunityModule"}}]}]}]';

	function index()
	{
		$this->BlockPage->recursive = false;
		$this->set('BlockPages', $this->BlockPage->find('all'));
	}

	function help()
	{
		if (Configure::read('debug') != 2) {
			header('Last-Modified: ' . gmdate('D, d M Y 00:00:00 \G\M\T', time() - 2 * 24 * 60 * 60));
			header('Expires: ' . gmdate('D, d M Y 11:11:11 \G\M\T', time() + 12 * 60 * 60));
			header('Cache-Control: public');
			header("Pragma: cache");
		}
		Configure::write('debug', '0');
		$moduleName = 'help';
		if (isset($this->params['named']['module'])) {
			if (!empty($this->params['named']['module'])) {
				if (file_exists(APP . 'views' . DS . 'blocks' . DS . strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $this->params['named']['module'])) . '.ctp')) {
					$moduleName = $this->params['named']['module'];
				}
			}
		}
		$this->render($moduleName, false);
	}

	function revisions($blockPageId = null)
	{
		if (!$blockPageId) {
			$this->Session->setFlash('Error: No revision specified!');
			$this->redirect(array('action' => 'index'));
		} else {
			if (isset($_POST['xhr']) && isset($this->params['named']['activate'])) {
				$this->autoRender = false;
				$revId = $this->params['named']['activate'];
				$this->BlockRevision->duplicate($blockPageId, $revId);
				echo "Ok!";
			} else {
				$this->set('blockPageId', $blockPageId);
				$this->set('blockPageUrl', $this->BlockPage->field('url', array('blockPageId' => $blockPageId)));
				$data = $this->BlockRevision->find('all', array(
					'fields' => array(
						'blockRevisionId',
						'sha1',
						'active',
						'editor',
						'created'
					),
					'conditions' => array('BlockPage.blockPageId' => $blockPageId)
				));
				foreach ($data as &$d) {
					$previewPath = 'http://www.luxurylink.com';
					if (strpos($_SERVER['HTTP_HOST'], 'toolboxdev') !== false) {
						$previewPath = 'http://' . str_replace('toolboxdev', 'lldev', $_SERVER['HTTP_HOST']);
					}
					$previewPath .= '/blocks/blocks.php?mode=preview&blockRevisionId=' . $d['BlockRevision']['blockRevisionId'];
					$previewPath .= '&blockSha1=' . $d['BlockRevision']['sha1'];
					$d['BlockRevision']['previewUrl'] = $previewPath;
				}
				$this->set('blockRevisions', $data);
			}
		}
	}

	function add()
	{
		$url = 'Unknown URL';
		if (isset($this->data['BlockPage']['url'])) {
			$url = BlockPageModule::filterURL($this->data['BlockPage']['url']);
			if ($blockPageId = $this->BlockPage->field('blockPageId', array('url' => $url))) {
				$this->Session->setFlash('Opening existing page: ' . htmlentities($url));
			} else {
				// Doesn't exist already, create it
				if ($url == '/') {
					$this->Session->setFlash('Error: Could not create page: ' . htmlentities($url));
					$this->redirect(array('action' => 'index'));
					return false;
				} else {
					$this->BlockPage->create(array(
						'url' => $url,
						'creator' => $this->getEditor()
					));
					$this->BlockPage->save();
					$blockPageId = $this->BlockPage->id;
					$this->BlockRevision->create(array(
						'blockPageId' => $blockPageId,
						'blockData' => $this->defaultTemplate,
						'editor' => $this->getEditor(),
					));
					$this->BlockRevision->save();
					$this->BlockRevision->activate($this->BlockPage->id, $this->BlockRevision->id);
					$this->Session->setFlash('Created page: ' . htmlentities($url));
				}
			}
			$this->redirect(array(
				'action' => 'edit',
				$blockPageId,
			));
		}
	}

	function edit($blockPageId = null)
	{
		if ($blockPageId && isset($_POST['treeData']) && !empty($_POST['treeData'])) {
			$this->BlockRevision->create(array(
				'blockData' => $_POST['treeData'],
				'blockPageId' => $blockPageId,
				'editor' => $this->getEditor(),
			));
			$this->BlockRevision->save();
			header('X-Blocks-PageId: ' . $blockPageId);
			header('X-Blocks-RevisionId: ' . $this->BlockRevision->id);
			$sha1 = $this->BlockRevision->field('sha1');
			header('X-Blocks-SHA1: ' . $sha1);
			$previewPath = 'http://www.luxurylink.com';
			if (strpos($_SERVER['HTTP_HOST'], 'toolboxdev') !== false) {
				$previewPath = 'http://' . str_replace('toolboxdev', 'lldev', $_SERVER['HTTP_HOST']);
			}
			if (isset($_POST['publish'])) {
				$this->BlockRevision->activate($blockPageId, $this->BlockRevision->id);
				$url = $this->BlockPage->field('url', array('blockPageId' => $blockPageId));
				$previewPath .= $url;
				$previewPath .= '?clearCache=' . sha1('LUXURY ' . $url . ' ' . date('Y-m-d') . ' LINK');
				header('X-Blocks-Publish: ' . $previewPath);
			} else {
				$previewPath .= '/blocks/blocks.php?mode=preview&blockRevisionId=' . $this->BlockRevision->id;
				$previewPath .= '&blockSha1=' . $sha1;
				header('X-Blocks-Preview: ' . $previewPath);
			}
		} else {
			if ($blockPageId) {
				$this->set('blockPageId', $blockPageId);
				$this->set('blockPageUrl', $this->BlockPage->field('url', array('blockPageId' => $blockPageId)));
				$blockData = $this->BlockRevision->field('blockData', array('blockPageId' => $blockPageId));
				if (!$blockData) {
					$blockData = $this->defaultTemplate;
				}
				$this->set('blockData', $blockData);
			} else {
				$this->Session->setFlash('Error: No revision specified!');
				$this->redirect(array('action' => 'index'));
			}
		}
	}

	private function getEditor()
	{
		// Returns the name of the logged in person
		if (isset($this->viewVars['user']['LdapUser']['username']) && !empty($this->viewVars['user']['LdapUser']['username'])) {
			return $this->editor = $this->viewVars['user']['LdapUser']['username'];
		}
		return '';
	}

	function preview()
	{
		$this->layout = 'blank';
	}

	function garbage($blockPageId)
	{
		if (!$blockPageId) {
			$this->Session->setFlash('Error: No revision specified!');
			$this->redirect(array('action' => 'index'));
		} else {
			echo "HI<pre>";
			$this->BlockRevision->recursive = -1;
			$data = $this->BlockRevision->find('all', array('conditions' => array('BlockRevision.blockPageId' => $blockPageId)));
			$allrevs = array();
			$safelist = array();
			$earliest = array();
			foreach ($data as $d) {
				$br = $d['BlockRevision'];
				$rev = $br['blockRevisionId'];
				$sha = $br['sha1'];
				$allrevs[$rev] = $rev;
				if ($br['active']) {
					$safelist[$rev] = $rev;
				}
				$earliest[$sha][$rev] = $rev;
			}
			foreach ($earliest as $l) {
				$min = min($l);
				$safelist[$min] = $min;
			}
			$hitlist = array_diff($allrevs, $safelist);
			foreach ($hitlist as $h) {
				$this->BlockRevision->delete($h);
			}
			$this->Session->setFlash('Garbage collected! (' . count($hitlist) . ' item' . (count($hitlist) == 1 ? '' : 's') . ' deleted)');
			$this->redirect(array(
				'action' => 'revisions',
				$blockPageId,
			));
		}
	}

}

/*
 * Create this instead of loading the real 'Module'
 */
class Module
{
	function __construct()
	{
	}

}
