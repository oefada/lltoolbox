<?php
class ImagesController extends AppController {

   var $name = 'Images';
   
   var $scaffold;
   
   function beforeFilter() {
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

	  $this->Image->imageTypes = array('gal-xl' => 1,		//slideshow
									   'gal-lrg' => 2,	 	//large (listing)
									   'list-sml' => 3	//	thumbnail
									   );

	  $this->fileRoot = dirname(dirname(dirname(dirname(__FILE__)))).'/luxurylink/php';
   }
   
   function organize() {
	  if (!empty($this->data)) {
		 if ($this->Image->ImageClient->saveOrganizedImages($this->data, $this->Image->clientId, $this->siteDbs)) {
			$this->Session->setFlash('Images have been saved.');
		 }
		 else {
			$this->Session->setFlash('Images could not be saved.');
		 }
	  }
	  $this->findNewImages();
	  foreach($this->Image->client['Client']['sites'] as $site) {
		 $this->Image->ImageClient->contain(array('Image', 'ImageType'));
		 $slideshowImages = $this->Image->ImageClient->find('all', array('conditions' => array('ImageClient.clientId' => $this->Image->clientId, 'ImageClient.imageTypeId' => 1, 'ImageClient.isHidden' => 0,  'ImageClient.siteId' => array_search($site, $this->siteDbs)),
																		   'order' => array('ImageClient.inactive', 'ImageClient.sortOrder'))
															);
		 $largeImages = $this->Image->ImageClient->find('all', array('conditions' => array('ImageClient.clientId' => $this->Image->clientId, 'ImageClient.imageTypeId' => 2, 'ImageClient.isHidden' => 0, 'ImageClient.siteId' => array_search($site, $this->siteDbs)),
																	 'order' => array('ImageClient.inactive'))
														 );
		 $thumbnailImages = $this->Image->ImageClient->find('all', array('conditions' => array('ImageClient.clientId' => $this->Image->clientId, 'ImageClient.imageTypeId' => 3, 'ImageClient.isHidden' => 0, 'ImageClient.siteId' => array_search($site, $this->siteDbs)),
																		   'order' => array('ImageClient.inactive'))
															);
		 $this->set('slideshowImages'.$site, $slideshowImages);
		 $this->set('largeImages'.$site, $largeImages);
		 $this->set('thumbnailImages'.$site, $thumbnailImages);
	  }
   }
   
   function captions() {
	  $this->Image->ImageClient->recursive = 2;
	  if (!empty($this->data)) {
		 $postImages = $this->data['Image'];
		 $images = $this->Image->ImageClient->find('all', array('conditions' => array('ImageClient.clientId' => $this->Image->clientId,
																					  'ImageClient.imageTypeId' => 1,
																				      'ImageClient.inactive' => 0),
															     'group' => array('ImageClient.imageId')
												));
		 foreach ($images as $image) {
			if (in_array($image['Image']['imageId'], array_keys($postImages))) {
			   $this->Image->saveCaptions($postImages[$image['Image']['imageId']], $image, $this->Image->clientId);
			   if (!empty($postImages[$image['Image']['imageId']]['RoomGradeId'])) {
				  $this->Image->ImageRoomGradeRel->saveImageRoomGrade($postImages[$image['Image']['imageId']]['RoomGradeId'], $image);
			   }
			   elseif (empty($postImages[$image['Image']['imageId']]['RoomGradeId']) && !empty($image['Image']['ImageRoomGradeRel'][0]['roomGradeId'])) {
				  $this->Image->ImageRoomGradeRel->deleteImageRoomGrade($image['Image']['ImageRoomGradeRel'][0]['imageRoomGradeRelId'], $image);
			   }
			}
		 }
	  }
	  $images = $this->Image->ImageClient->find('all', array('conditions' => array('ImageClient.clientId' => $this->Image->clientId,
																				   'ImageClient.imageTypeId' => 1,
														 					       'ImageClient.inactive' => 0),
															 'order' => array('Image.caption'),
															 'group' => array('ImageClient.imageId')
												));
	  $this->Image->ImageRoomGradeRel->RoomGrade->recursive = -1;
	  $roomGrades = $this->Image->ImageRoomGradeRel->RoomGrade->find('all', array('conditions' => array('RoomGrade.clientId' => $this->Image->clientId),
																				   'order' => array('RoomGrade.roomGradeName')));
	  $this->set('images', $images);
	  $this->set('roomGrades', $roomGrades);
   }
   
   function slideshow() {
	  if (!empty($this->data)) {
		 if ($this->Image->ImageClient->saveAll($this->data['ImageClient'])) {
			$this->Session->setFlash('Your captions have been saved');
		 }
		 else {
			$this->Session->setFlash('Your captions could not be saved');
		 }
	  }
	  foreach($this->Image->client['Client']['sites'] as $site) {
		 $this->Image->ImageClient->contain('Image');
		 $images = $this->Image->ImageClient->find('all', array('conditions' => array('ImageClient.clientId' => $this->Image->clientId,
																					   'ImageClient.imageTypeId' => 1,
																					   'ImageClient.inactive' => 0,
																					   'ImageClient.siteId' => array_search($site, $this->siteDbs)),
																 'order' => array('ImageClient.sortOrder')
																 )
												   );
		 $this->set('images'.$site, $images);
	  }
   }
   
   function delete_images() {
	  if (!empty($this->data)) {
		 foreach($this->data['ImageClient'] as $image) {
			$data = $image;
			$data['isHidden'] = 1;
			$data['inactive'] = 1;
			foreach($this->Image->client['Client']['sites'] as $site) {
			   if (isset($data['clientImageId'])) {
				  unset($data['clientImageId']);
			   }
			   $siteImage = $this->Image->ImageClient->find('first', array('conditions' => array('ImageClient.imageId' => $image['imageId'],
																								 'ImageClient.clientId' => $this->Image->clientId,
																								 'ImageClient.siteId' => array_search($site, $this->siteDbs)),
																		   'fields' => 'ImageClient.clientImageId'));
			   $data['clientImageId'] = $siteImage['ImageClient']['clientImageId'];
			   $this->Image->ImageClient->save($data);
			}
		 }
	  }
	  $this->Image->ImageClient->contain('Image');
	  $images = $this->Image->ImageClient->find('all', array('fields' => array('ImageClient.imageId', 'Image.imagePath', 'GROUP_CONCAT(ImageClient.inactive ORDER BY ImageClient.siteId) AS inactive', 'GROUP_CONCAT(ImageClient.siteId ORDER BY ImageClient.siteId) AS sites'),
															 'conditions' => array('ImageClient.clientId' => $this->Image->clientId,
																				   'ImageClient.isHidden' => 0),
															  'order' => array('ImageClient.siteId'),
															  'group' => array('ImageClient.imageId')
															  )
												);
	  foreach($images as &$image) {
		 $sitesArr = explode(',', $image[0]['sites']);
		 $inactiveArr = explode(',', $image[0]['inactive']);
		 $siteStr = '';
		 $i = 0;
		 foreach($sitesArr as $site) {
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
   
   function findNewImages() {
	  $dbImages = $this->Image->getFilenamesFromDb($this->Image->clientId);
	  if (!empty($dbImages)) {
		 $extractDir = explode('/', $dbImages[0]);
		 array_pop($extractDir);
		 $directory = implode('/', $extractDir);
	  }
	  else {
		 $directory = '/images/por/'.$this->Image->client['Client']['oldProductId'];
	  }
	  $files = glob($this->fileRoot.$directory.'/*.jpg');
	  $useLrgForSlideshow = false;
	  if (!in_array($this->fileRoot.$directory.'/'.$this->Image->client['Client']['oldProductId'].'-gal-xl-01.jpg', $files)) {
		 $useLrgForSlideshow = true;
	  }
	  if (!empty($files)) {
		 $siteId = array_search($this->Image->client['Client']['sites'][0], $this->siteDbs);
		 foreach($files as $file) {
			$image = split($this->fileRoot, $file);
			$imagePath = $image[1];
			if (!in_array($imagePath, $dbImages)) {
			   $imageTypeId = 0;
			   foreach($this->Image->imageTypes as $fileType => $value) {
				  if (stristr($file, $fileType)) {
					 $imageTypeId = $value;
					 break;
				  }
			   }
			   if ($imageTypeId > 0 ) {
				  $clientId = $this->Image->clientId;
				  $inactive = 1;
				  $imageId = $this->Image->createFromFile(compact('imagePath', 'clientId', 'imageTypeId', 'siteId', 'inactive'));
				  if ($imageTypeId == 2 && $useLrgForSlideshow) {
					 $imageTypeId = 1;
					 $this->Image->createFromFile(compact('imagePath', 'clientId', 'imageTypeId', 'siteId', 'inactive', 'imageId'));
				  }
			   }
			}
		 }
	  }
   }

}
?>