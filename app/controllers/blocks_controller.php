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
			$this->BlockRevision->activate($blockPageId, $this->BlockRevision->id);
		} else {
			if ($blockPageId) {
				$this->set('blockPageId', $blockPageId);
				$blockData = $this->BlockRevision->field('blockData', array(
					'blockPageId' => $blockPageId,
					'active' => 1
				));
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
