<?php
class ImagesController extends AppController
{

	var $name = 'Images';

	var $scaffold;

	function beforeFilter()
	{
		parent::beforeFilter();
		$this->set('currentTab', 'property');
		if (isset($this->params['clientId']) && empty($clientId)) {
			$this->Image->clientId = $this->params['clientId'];
		} else {
			$this->params['clientId'] = $this->Image->clientId;
		}
		$this->Image->client = $this->Image->ImageClient->Client->findByClientId($this->Image->clientId);
		$this->set('clientId', $this->Image->clientId);
		$this->set('client', $this->Image->client);
		$this->set('clientSites', $this->Image->client['Client']['sites']);

		$this->Image->imageTypes = array(
			'gal-xl' => 1, //slideshow
			'gal-lrg' => 2, //large (listing)
			'list-sml' => 3, //	thumbnail
			'-auto-' => 1, // new PHOtos
		);

		$this->fileRoot = dirname(dirname(dirname(dirname(__FILE__)))) . '/luxurylink/php';
	}

	function organize()
	{
		if (!empty($this->data)) {
			$this->set('displayTab', $this->data['saveSite']);
			unset($this->data['saveSite']);
			if ($this->Image->ImageClient->saveOrganizedImages($this->data, $this->Image->clientId, $this->siteDbs)) {
				$this->Session->setFlash('Images have been saved.');
			} else {
				$this->Session->setFlash('Images could not be saved.');
			}
		} else {
			if (count($this->Image->client['Client']['sites']) > 1) {
				$this->set('displayTab', 'luxurylink');
			} else {
				if (isset($this->Image->client['Client']['sites'][0])) {
					$this->set('displayTab', $this->Image->client['Client']['sites'][0]);
				} else {
					$this->set('displayTab', 'luxurylink');
				}
			}
			$this->findNewImages();
		}
		//foreach($this->Image->client['Client']['sites'] as $site) {
		foreach ($this->siteDbs as $siteId => $siteDb) {
			$this->Image->ImageClient->contain(array(
				'Image',
				'ImageType'
			));
			$slideshowImages = $this->Image->ImageClient->find('all', array(
				'conditions' => array(
					'ImageClient.clientId' => $this->Image->clientId,
					'ImageClient.imageTypeId' => 1,
					'ImageClient.isHidden' => 0,
					'ImageClient.siteId' => $siteId
				),
				'order' => array(
					'ImageClient.inactive',
					'ImageClient.sortOrder'
				)
			));
			foreach ($slideshowImages as &$ssi) {
				if (isset($ssi['Image']['imagePath'])) {
					if (is_file('/mnt' . $ssi['Image']['imagePath'])) {
						$ssi['Image']['filemtime'] = filemtime('/mnt' . $ssi['Image']['imagePath']);
					} else {
						$ssi['Image']['filemtime'] = 69;
					}
				}
			}
			$largeImages = $this->Image->ImageClient->find('all', array(
				'conditions' => array(
					'ImageClient.clientId' => $this->Image->clientId,
					'ImageClient.imageTypeId' => 2,
					'ImageClient.isHidden' => 0,
					'ImageClient.siteId' => $siteId
				),
				'order' => array('ImageClient.inactive')
			));
			$thumbnailImages = $this->Image->ImageClient->find('all', array(
				'conditions' => array(
					'ImageClient.clientId' => $this->Image->clientId,
					'ImageClient.imageTypeId' => 3,
					'ImageClient.isHidden' => 0,
					'ImageClient.siteId' => $siteId
				),
				'order' => array('ImageClient.inactive')
			));
			$this->set('slideshowImages' . $siteDb, $slideshowImages);
			$this->set('largeImages' . $siteDb, $largeImages);
			$this->set('thumbnailImages' . $siteDb, $thumbnailImages);
		}
	}

	function captions()
	{
		$this->Image->ImageClient->recursive = 2;
		if (!empty($this->data)) {
			$postImages = $this->data['Image'];
			$images = $this->Image->ImageClient->find('all', array(
				'conditions' => array(
					'ImageClient.clientId' => $this->Image->clientId,
					'ImageClient.imageTypeId' => 1,
					'ImageClient.inactive' => 0
				),
				'group' => array('ImageClient.imageId')
			));
			foreach ($images as $image) {
				if (in_array($image['Image']['imageId'], array_keys($postImages))) {
					$this->Image->saveCaptions($postImages[$image['Image']['imageId']], $image, $this->Image->clientId);
					if (!empty($postImages[$image['Image']['imageId']]['RoomGradeId'])) {
						$this->Image->ImageRoomGradeRel->saveImageRoomGrade($postImages[$image['Image']['imageId']]['RoomGradeId'], $image);
					} elseif (empty($postImages[$image['Image']['imageId']]['RoomGradeId']) && !empty($image['Image']['ImageRoomGradeRel'][0]['roomGradeId'])) {
						$this->Image->ImageRoomGradeRel->deleteImageRoomGrade($image['Image']['ImageRoomGradeRel'][0]['imageRoomGradeRelId'], $image);
					}
				}
			}
		}
		$images = $this->Image->ImageClient->find('all', array(
			'conditions' => array(
				'ImageClient.clientId' => $this->Image->clientId,
				'ImageClient.imageTypeId' => 1,
				'ImageClient.inactive' => 0
			),
			'order' => array('Image.caption'),
			'group' => array('ImageClient.imageId')
		));
		$this->Image->ImageRoomGradeRel->RoomGrade->recursive = -1;
		$roomGrades = $this->Image->ImageRoomGradeRel->RoomGrade->find('all', array(
			'conditions' => array('RoomGrade.clientId' => $this->Image->clientId),
			'order' => array('RoomGrade.roomGradeName')
		));
		$this->set('images', $images);
		$this->set('roomGrades', $roomGrades);
	}

	function slideshow()
	{
		if (!empty($this->data)) {
			if ($this->Image->ImageClient->saveAll($this->data['ImageClient'])) {
				$this->Session->setFlash('Your captions have been saved');
			} else {
				$this->Session->setFlash('Your captions could not be saved');
			}
		}
		//foreach($this->Image->client['Client']['sites'] as $site) {
		foreach ($this->siteDbs as $siteId => $siteDb) {
			$this->Image->ImageClient->contain('Image');
			$images = $this->Image->ImageClient->find('all', array(
				'conditions' => array(
					'ImageClient.clientId' => $this->Image->clientId,
					'ImageClient.imageTypeId' => 1,
					'ImageClient.inactive' => 0,
					'ImageClient.siteId' => $siteId
				),
				'order' => array('ImageClient.sortOrder')
			));
			$this->set('images' . $siteDb, $images);
		}
	}

	function delete_images()
	{
		if (!empty($this->data)) {
			foreach ($this->data['ImageClient'] as $image) {
				$data = $image;
				$data['isHidden'] = 1;
				$data['inactive'] = 1;
				//foreach($this->Image->client['Client']['sites'] as $site) {
				foreach ($this->siteDbs as $siteId => $siteDb) {
					if (isset($data['clientImageId'])) {
						unset($data['clientImageId']);
					}
					$siteImage = $this->Image->ImageClient->find('first', array(
						'conditions' => array(
							'ImageClient.imageId' => $image['imageId'],
							'ImageClient.clientId' => $this->Image->clientId,
							'ImageClient.siteId' => $siteId
						),
						'fields' => 'ImageClient.clientImageId'
					));
					$data['clientImageId'] = $siteImage['ImageClient']['clientImageId'];
					$this->Image->ImageClient->save($data);
				}
			}
		}
		$this->Image->ImageClient->contain('Image');
		$images = $this->Image->ImageClient->find('all', array(
			'fields' => array(
				'ImageClient.imageId',
				'Image.imagePath',
				'GROUP_CONCAT(ImageClient.inactive ORDER BY ImageClient.siteId) AS inactive',
				'GROUP_CONCAT(ImageClient.siteId ORDER BY ImageClient.siteId) AS sites'
			),
			'conditions' => array(
				'ImageClient.clientId' => $this->Image->clientId,
				'ImageClient.isHidden' => 0
			),
			'order' => array('ImageClient.siteId'),
			'group' => array('ImageClient.imageId')
		));
		foreach ($images as &$image) {
			$sitesArr = explode(',', $image[0]['sites']);
			$inactiveArr = explode(',', $image[0]['inactive']);
			$siteStr = '';
			$i = 0;
			foreach ($sitesArr as $site) {
				$siteStr .= '<div>';
				$siteStr .= ($site == 1) ? 'LL: ' : 'Family: ';
				$siteStr .= ($inactiveArr[$i] == 0) ? 'Active' : 'Inactive';
				$siteStr .= '</div>';
				$i++;
			}
			$image['ImageClient']['siteStr'] = $siteStr;
		}
		$this->set('images', $images);
	}

	function findNewImages()
	{
		if (false) {
			// TEMPORARILY DISABLE FINDING ANY NEW IMAGES IN TOOLBOX DURING MIGRATION;
			return false;
		}
		$dbImages = $this->Image->getFilenamesFromDb($this->Image->clientId);
		if (!empty($dbImages)) {
			$extractDir = explode('/', $dbImages[0]);
			array_pop($extractDir);
			$directory = implode('/', $extractDir);
			$oldProductId = (empty($this->Image->client['Client']['oldProductId'])) ? '0-' . $this->Image->client['Client']['clientId'] : $this->Image->client['Client']['oldProductId'];
		} else {
			if (empty($this->Image->client['Client']['oldProductId'])) {
				$oldProductId = $this->Image->client['Client']['clientId'];
				$directory = '/images/por/0-' . $oldProductId;
			} else {
				$oldProductId = $this->Image->client['Client']['oldProductId'];
				$directory = '/images/por/' . $oldProductId;
			}

		}
		if (false) {
			// DEAD CODE, DO NOT NEED TO READ FROM POR DIRECTORY ANYMORE
			$files = glob($this->fileRoot . $directory . '/*.jpg');
		} else {
			$files = array();
		}
		$useLrgForSlideshow = false;
		$useXlForSlideshow = false;
		$activateLrg = false;
		$activateXl = false;
		if (!in_array($this->fileRoot . $directory . '/' . $oldProductId . '-gal-xl-01.jpg', $files) && !$this->Image->getValidXls($this->Image->clientId)) {
			$useLrgForSlideshow = true;
		} elseif (in_array($this->fileRoot . $directory . '/' . $oldProductId . '-gal-xl-01.jpg', $files) && !in_array($directory . '/' . $oldProductId . '-gal-xl-01.jpg', $dbImages)) {
			$useXlForSlideshow = true;
			if ($lrgSlideshowImages = $this->Image->getLrgSlideshow($this->Image->client['Client']['clientId'])) {
				$this->Image->inactivateLrgSlideshow($lrgSlideshowImages);
				$activateXl = true;
			}
		} elseif (in_array($this->fileRoot . $directory . '/' . $oldProductId . '-gal-xl-01.jpg', $files) && !$this->Image->getValidXls($this->Image->clientId)) {
			$useLrgForSlideshow = true;
			$activateLrg = true;
		}

		$newFiles = glob($this->fileRoot . '/images/pho/' . $this->Image->clientId . '/' . $this->Image->clientId . '_*-auto-*.jpg');
		if (!empty($newFiles)) {
			$useXlForSlideshow = true;
			foreach ($newFiles as $newFile) {
				if (!in_array($newFile, $dbImages)) {
					$files[] = $newFile;
				}
			}
		}

		if (!empty($files)) {
			//$siteId = array_search($this->Image->client['Client']['sites'][0],
			// $this->siteDbs);
			foreach ($files as $file) {
				$image = split($this->fileRoot, $file);
				$imagePath = $image[1];
				if (!in_array($imagePath, $dbImages)) {
					$imageTypeId = 0;
					foreach ($this->Image->imageTypes as $fileType => $value) {
						if (stristr($file, $fileType)) {
							$imageTypeId = $value;
							break;
						}
					}
					if ($imageTypeId > 0) {
						$clientId = $this->Image->clientId;
						$inactive = 1;
						if (stristr($file, 'xl') && $imageTypeId == 1 && $activateXl) {
							$inactive = 0;
						} elseif (stristr($file, 'lrg') && $imageTypeId == 1 && $activateLrg) {
							$inactive = 0;
						}
						foreach ($this->siteDbs as $siteId => $siteDb) {
							$this->Image->recursive = -1;
							$image = $this->Image->find('first', array(
								'conditions' => array('imagePath' => $imagePath),
								'fields' => 'imageId'
							));
							if (empty($image)) {
								$imageId = $this->Image->createFromFile(compact('imagePath', 'clientId', 'imageTypeId', 'siteId', 'inactive'));
							} else {
								$imageId = $image['Image']['imageId'];
								$imageId = $this->Image->createFromFile(compact('imageId', 'imagePath', 'clientId', 'imageTypeId', 'siteId', 'inactive'));
							}
							if ($imageTypeId == 2 && $useLrgForSlideshow) {
								$imageTypeId = 1;
								$this->Image->createFromFile(compact('imagePath', 'clientId', 'imageTypeId', 'siteId', 'inactive', 'imageId'));
								$imageTypeId = 2;
							}
						}
					}
				}
			}
		}
	}

	function deduplicate()
	{
		$sql = 'SELECT DISTINCT clientId FROM (SELECT clientId,siteId,imageId,COUNT(clientImageId) AS c FROM imageClient WHERE imageTypeId=1 GROUP BY siteId,clientId,imageId ORDER BY COUNT(clientImageId) DESC) t1 WHERE c>1 ORDER BY clientId';
		$this->set('dupes', $this->Image->query($sql));
	}

}
