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
	var $defaultTemplate = '[{"data": "New Page", "attr": {"data-blocks": "{\"meta_title\":\"New Page\"}", "rel": "BlockPageModule"}, "state": "open", "metadata": {}, "children": [{"data": "full", "attr": {"data-blocks": "{\"src\":\"http://placekitten.com/960/270\"}", "rel": "BlockLayoutModule"}, "state": "open", "metadata": {}, "children": [{"data": "BlockPhotoModule", "attr": {"rel": "BlockPhotoModule"}, "state": "open", "metadata": {}, "children": [{"data": "BlockImageModule", "attr": {"data-blocks": "{\"src\":\"http://placekitten.com/960/270\"}", "rel": "BlockImageModule"}, "metadata": {}}]}]}, {"data": "content", "attr": {"data-blocks": "{\"class\":\"content\"}", "rel": "BlockLayoutModule"}, "state": "open", "metadata": {}, "children": [{"data": "BlockDivModule", "attr": {"rel": "BlockDivModule"}, "state": "open", "metadata": {}, "children": [{"data": "New Page", "attr": {"data-blocks": "{\"meta_title\":\"New Page\"}", "rel": "BlockHeaderModule"}, "metadata": {}}, {"data": "Lorem ipsum dolor si...", "attr": {"data-blocks": "{\"content\":\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum semper tristique lorem, vitae ultrices orci dapibus a. Quisque lacinia lorem mi. Mauris tincidunt fermentum arcu, nec tempor nisi porttitor at. Maecenas sagittis dui a mi porta lacinia. Donec dapibus, lorem ac aliquet lacinia, arcu nibh rutrum leo, in volutpat nisi elit et orci. Maecenas venenatis eros vitae ipsum posuere hendrerit. Ut sollicitudin metus eu nulla consequat vestibulum. Proin id faucibus risus. Donec aliquam scelerisque diam, id adipiscing urna porttitor vel. Nunc commodo, justo vitae viverra pellentesque, nisl tortor egestas neque, ut fringilla urna mauris eu quam. Nunc cursus leo leo, commodo egestas justo. Etiam commodo volutpat congue. Vestibulum varius, ligula non molestie dignissim, massa nisi pretium tortor, sit amet tristique neque elit in nulla.\"}", "rel": "BlockParagraphModule"}, "metadata": {}}]}, {"data": "Lorem ipsum dolor si...", "attr": {"data-blocks": "{\"content\":\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum semper tristique lorem, vitae ultrices orci dapibus a. Quisque lacinia lorem mi. Mauris tincidunt fermentum arcu, nec tempor nisi porttitor at. Maecenas sagittis dui a mi porta lacinia. Donec dapibus, lorem ac aliquet lacinia, arcu nibh rutrum leo, in volutpat nisi elit et orci. Maecenas venenatis eros vitae ipsum posuere hendrerit. Ut sollicitudin metus eu nulla consequat vestibulum. Proin id faucibus risus. Donec aliquam scelerisque diam, id adipiscing urna porttitor vel. Nunc commodo, justo vitae viverra pellentesque, nisl tortor egestas neque, ut fringilla urna mauris eu quam. Nunc cursus leo leo, commodo egestas justo. Etiam commodo volutpat congue. Vestibulum varius, ligula non molestie dignissim, massa nisi pretium tortor, sit amet tristique neque elit in nulla.\"}", "rel": "BlockDivModule"}, "state": "open", "metadata": {}, "children": [{"data": "BlockTabsModule", "attr": {"data-blocks": "{\"content\":\"First tab content\"}", "rel": "BlockTabsModule"}, "state": "open", "metadata": {}, "children": [{"data": "First Tab", "attr": {"data-blocks": "{\"title\":\"First Tab\"}", "rel": "BlockTabModule"}, "state": "open", "metadata": {}, "children": [{"data": "First tab content", "attr": {"data-blocks": "{\"content\":\"First tab content\"}", "rel": "BlockDivModule"}, "metadata": {}}]}, {"data": "Second Tab", "attr": {"data-blocks": "{\"title\":\"Second Tab\"}", "rel": "BlockTabModule"}, "state": "open", "metadata": {}, "children": [{"data": "Second tab content", "attr": {"data-blocks": "{\"content\":\"Second tab content\"}", "rel": "BlockDivModule"}, "metadata": {}}]}]}]}, {"data": "BlockDivModule", "attr": {"rel": "BlockDivModule"}, "state": "open", "metadata": {}, "children": [{"data": "NewsletterModule", "attr": {"data-blocks": "{\"type\":\"NewsletterModule\"}", "rel": "BlockPrefabModule"}, "metadata": {}}]}]}, {"data": "sidebar", "attr": {"data-blocks": "{\"class\":\"sidebar\"}", "rel": "BlockLayoutModule"}, "state": "open", "metadata": {}, "children": [{"data": "FeaturedAuctionsModu...", "attr": {"data-blocks": "{\"type\":\"FeaturedAuctionsModule\"}", "rel": "BlockPrefabModule"}, "metadata": {}}, {"data": "CommunityModule", "attr": {"data-blocks": "{\"type\":\"CommunityModule\"}", "rel": "BlockPrefabModule"}, "metadata": {}}]}]}]';

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
