<?php
class ImageClient extends AppModel
{

	var $name = 'ImageClient';
	var $useTable = 'imageClient';
	var $primaryKey = 'clientImageId';

	var $actsAs = array('Containable');

	var $multisite = true;
	var $containModels = array('Image');

	var $belongsTo = array(
		'Client' => array(
			'className' => 'Client',
			'foreignKey' => 'clientId'
		),
		'Image' => array(
			'className' => 'Image',
			'foreignKey' => 'imageId'
		),
		'ImageType' => array(
			'className' => 'ImageType',
			'foreignKey' => 'imageTypeId'
		)
	);

	function saveOrganizedImages($data, $clientId, $sitesMap)
	{
		$i = 1;
		if (!empty($data['duplicateTo'])) {
			$sitesToDuplicateTo = array_keys($data['duplicateTo']);
		}
		foreach ($data['ImageClient'] as $imageClientId => $image) {
			$imageClient = $image;
			$imageClient['clientImageId'] = $imageClientId;
			$imageClient['clientId'] = $clientId;
			if (!isset($image['inactive'])) {
				$imageClient['inactive'] = 1;
			} elseif ($image['inactive'] == 'on') {
				$imageClient['inactive'] = 0;
			}
			if (isset($image['imageTypeId']) && $image['imageTypeId'] == 1 && $image['inactive'] == 0) {
				$imageClient['sortOrder'] = $i;
				$i++;
			} else {
				$imageClient['sortOrder'] = null;
			}
			$this->create();
			$this->data['ImageClient'] = $imageClient;
			if ($this->save($this->data)) {
				if (!empty($sitesToDuplicateTo)) {
					foreach ($sitesToDuplicateTo as $site) {
						unset($imageClient['clientImageId']);
						$dupeImage = $this->find('first', array(
							'conditions' => array(
								'ImageClient.imageId' => $imageClient['imageId'],
								'ImageClient.clientId' => $clientId,
								'ImageClient.imageTypeId' => $imageClient['imageTypeId'],
								'ImageClient.siteId' => array_search($site, $sitesMap)
							),
							'fields' => array('ImageClient.clientImageId')
						));
						if (!empty($dupeImage)) {
							$imageClient['clientImageId'] = $dupeImage['ImageClient']['clientImageId'];
						} else {
							$originalImage = $this->find('first', array(
								'conditions' => array('ImageClient.clientImageId' => $imageClientId),
								'fields' => array('ImageClient.caption')
							));
							$imageClient['caption'] = $originalImage['ImageClient']['caption'];
						}
						$imageClient['siteId'] = array_search($site, $sitesMap);
						$this->create();
						$this->data['ImageClient'] = $imageClient;
						if ($this->save($this->data)) {
							continue;
						} else {
							return false;
						}
					}
				}
			} else {
				return false;
			}
		}
		return true;
	}

	function getFirstImagePath($clientId)
	{
		$results = $this->find('first', array(
			'order' => 'ImageClient.sortOrder ASC,ImageClient.clientImageId ASC',
			'conditions' => array(
				'ImageClient.clientId' => $clientId,
				'ImageClient.isHidden' => 0,
				'ImageClient.inactive' => 0,
				'ImageClient.imageTypeId' => 1,
			)
		));
		return isset($results['Image']['imagePath']) ? $results['Image']['imagePath'] : false;
	}

}
