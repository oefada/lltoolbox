<?php

$loadModules = array(
    'BlockMarkupModule',
    'BlockPageModule',
    'BlockDivModule',
);
foreach ($loadModules as $blockModuleName) {
    App::import(
        'Vendor',
        $blockModuleName,
        array('file' => 'appshared' . DS . 'modules' . DS . $blockModuleName . DS . $blockModuleName . '.php')
    );
}

class BlocksController extends AppController
{
    var $name = 'Blocks';
    var $helpers = array('Html');
    var $uses = array(
        'BlockPage',
        'BlockRevision',
    );
    var $defaultTemplate = '[]';

    function index()
    {
        $this->BlockPage->recursive = false;
        $this->set('BlockPages', $this->BlockPage->find('all'));
    }

    function ping()
    {
        $this->autoLayout = false;
        Configure::write('debug', '0');
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
                if (file_exists(
                    APP . 'views' . DS . 'blocks' . DS . strtolower(
                        preg_replace('/([a-z])([A-Z])/', '$1_$2', $this->params['named']['module'])
                    ) . '.ctp'
                )
                ) {
                    $moduleName = $this->params['named']['module'];
                }
            }
        }
        $this->render($moduleName, false);
    }

    function images()
    {
        $this->autoLayout = false;
        Configure::write('debug', '0');
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
                $data = $this->BlockRevision->find(
                    'all',
                    array(
                        'fields' => array(
                            'blockRevisionId',
                            'sha1',
                            'active',
                            'editor',
                            'created'
                        ),
                        'conditions' => array('BlockPage.blockPageId' => $blockPageId)
                    )
                );
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
                $this->BlockPage->create(
                    array(
                        'url' => $url,
                        'creator' => $this->getEditor()
                    )
                );
                $this->BlockPage->save();
                $blockPageId = $this->BlockPage->id;
                $startingBlockData = '';
                if (isset($this->data['BlockPage']['templatePageId']) && $this->data['BlockPage']['templatePageId']) {
                    $startingBlockData = $this->BlockRevision->field(
                        'blockData',
                        array('blockPageId' => $this->data['BlockPage']['templatePageId'])
                    );
                }
                if (!$startingBlockData) {
                    $startingBlockData = $this->defaultTemplate;
                }
                $this->BlockRevision->create(
                    array(
                        'blockPageId' => $blockPageId,
                        'blockData' => $startingBlockData,
                        'editor' => $this->getEditor(),
                    )
                );
                $this->BlockRevision->save();
                $this->BlockRevision->activate($this->BlockPage->id, $this->BlockRevision->id);
                $this->Session->setFlash('Created page: ' . htmlentities($url));
            }
            $this->redirect(
                array(
                    'action' => 'edit',
                    $blockPageId,
                )
            );
        }
    }

    function edit($blockPageId = null)
    {
        if ($blockPageId && isset($_POST['treeData']) && !empty($_POST['treeData'])) {
            $this->BlockRevision->create(
                array(
                    'blockData' => $_POST['treeData'],
                    'blockPageId' => $blockPageId,
                    'editor' => $this->getEditor(),
                )
            );
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
                
                $forceUK = false;
                if (substr($url, -2) == '-2') {
                	$url = substr($url, 0, -2);
                	$forceUK = true;
                }
                
                $previewPath .= $url;
                $previewPath .= '?clearCache=' . sha1('LUXURY ' . $url . ' ' . date('Y-m-d') . ' LINK');
                if ($forceUK) {
                	$previewPath .= '&forceUK';
                }
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
                $this->set('blockData', bin2hex($blockData));
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
            $data = $this->BlockRevision->find(
                'all',
                array('conditions' => array('BlockRevision.blockPageId' => $blockPageId))
            );
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
            $this->Session->setFlash(
                'Garbage collected! (' . count($hitlist) . ' item' . (count($hitlist) == 1 ? '' : 's') . ' deleted)'
            );
            $this->redirect(
                array(
                    'action' => 'revisions',
                    $blockPageId,
                )
            );
        }
    }

    function tidy()
    {
        $this->autoRender = false;
        header('Content-type: application/json');
        Configure::write('debug', 0);
        $data = array();
        if (isset($_POST['validate']) && is_array($_POST['validate'])) {
            foreach ($_POST['validate'] as $k => $v) {
                $z = array();
                $z['source'] = $v;
                $config = array(
                    'indent' => true,
                    'indent-spaces' => 2,
                    'tab-size' => 2,
                    'output-xhtml' => true,
                    'wrap' => 200
                );
                $v = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><title></title></head><body>' . "\n" . $v;
                $v = $v . "\n" . '</body></html>';
                $tidy = tidy_parse_string($v, $config, 'UTF8');
                $tidy->cleanRepair();
                $clean = strval($tidy);
                if ($tidy->errorBuffer) {
                    $z['error'] = $tidy->errorBuffer;
                    $z['error'] = preg_replace_callback(
                        '/line ([0-9]+) column /',
                        create_function('$matches', 'return "line ".(intval($matches[1])-1)." column ";'),
                        $z['error']
                    );
                    if (strpos($z['error'], 'Error:') !== false) {
                        $z['background'] = '#ffcccc';
                    } else {
                        $z['background'] = '#eeeeff';
                    }
                } else {
                    $z['background'] = '#eeffee';
                }
                $clean = preg_replace('/.*\<body[^>]*\>/s', '', $clean);
                $clean = preg_replace('/<\/body\>.*/s', '', $clean);
                $clean = preg_replace('/^    /m', '', $clean);
                $z['tidied'] = trim($clean);
                $data[$k] = $z;
            }
        }
        echo json_encode(array('cleanroom' => $data));
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
