<?php
class ImagesController extends AppController
{
    public $name = 'Images';
    public $uses = array(
        'Image',
        'ImageClient',
        'ImageRoomGradeRel',
        'Client'
    );

    public $scaffold;

    /**
     *
     */
    public function beforeFilter()
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

    /**
     *
     */
    public function organize()
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

        $showImageRequest = false;
        foreach ($this->Image->client['Loa'] as $loa) {
            $endDate = strtotime($loa['endDate']);
            if ($endDate > time()) {
                $showImageRequest = true;
            }
        }
        if ($this->Client->isPHG($this->Image->clientId)) {
            $showImageRequest = false;
        }
        $this->set('showImageRequest', $showImageRequest);
    }

    /**
     *
     */
    public function captions()
    {
        if (!empty($this->data)) {
            foreach ($this->data['ImageClient'] as $ic) {
                // caption

                //copy to family is checked.
                if (isset($this->data['ImageClient']['copy_to_family']) && $this->data['ImageClient']['copy_to_family'] == 1) {

                    $newSiteId = 2;

                    //check to see if image exist on other site (family getaway)
                    $imgDataSite = $this->ImageClient->findActiveImagebySiteIdClientId(
                        $ic['imageId'],
                        $newSiteId,
                        $this->Image->clientId
                    );

                    if (empty($imgDataSite)) {

                    } else {
                        //update the other site data with the new caption
                        $this->ImageClient->updateCaptionbyOtherSiteId(
                            $this->Image->clientId,
                            $newSiteId,
                            $ic['imageId'],
                            $ic['caption']
                        );
                    }
                }

                //the captions seem to only get update if there was a change.
                if ($ic['caption'] != $ic['currentCaption']) {
                    $caption = array('clientImageId' => $ic['clientImageId'], 'caption' => $ic['caption']);


                    $this->ImageClient->save(array('ImageClient' => $caption));

                    // DANGER, Will Robinson, dirty hack ahead!
                    // This changes the caption on the image table so changes to caption in the toolbox
                    // are reflected on luxurylink.com
                    $tempPostData['caption'] = $ic['caption'];
                    $tempImageData['Image']['imageId'] = $ic['imageId'];
                    $tempClientId = $this->Image->clientId;

                    $this->Image->saveCaptions($tempPostData, $tempImageData, $tempClientId);
                }

                // room grade
                if ($ic['roomGradeId'] != $ic['currentRoomGrade']) {
                    $this->Image->recursive = 1;
                    $rgImage = $this->Image->find(
                        'first',
                        array(
                            'conditions' => array(
                                'Image.imageId' => $ic['imageId']
                            )
                        )
                    );
                    $rgImage['ImageClient'] = $rgImage['ImageClient'][0];
                    $rgImage['Image']['ImageRoomGradeRel'] = $rgImage['ImageRoomGradeRel'];
                    unset($rgImage['ImageRoomGradeRel']);

                    if ($ic['roomGradeId'] > 0) {
                        $this->Image->ImageRoomGradeRel->saveImageRoomGrade($ic['roomGradeId'], $rgImage);
                    } else {
                        $relId = intval($rgImage['Image']['ImageRoomGradeRel'][0]['imageRoomGradeRelId']);
                        if ($relId > 0) {
                            $this->Image->ImageRoomGradeRel->deleteImageRoomGrade($relId, $rgImage);
                        }
                    }
                }

                $this->Session->setFlash('Your updates have been saved.');
            }
        }

        $q = 'SELECT * FROM imageClient ImageClient
			  INNER JOIN image Image USING(imageId)
			  LEFT JOIN imageRoomGradeRel ImageRoomGradeRel  USING(imageId)
			  WHERE ImageClient.clientId = ?
			  AND ImageClient.imageTypeId = 1
			  AND ImageClient.inactive = 0';
        $images = $this->Image->query($q, array($this->Image->clientId));

        $imagesLL = $imagesFG = array();
        foreach ($images as $thisImg) {
            if ($thisImg['ImageClient']['siteId'] == 1) {
                $imagesLL[] = $thisImg;
            } elseif ($thisImg['ImageClient']['siteId'] == 2) {
                $imagesFG[] = $thisImg;
            }
        }
        $this->set('imagesluxurylink', $imagesLL);
        $this->set('imagesfamily', $imagesFG);

        $this->Image->ImageRoomGradeRel->RoomGrade->recursive = -1;
        $roomGrades = $this->Image->ImageRoomGradeRel->RoomGrade->find(
            'all',
            array(
                'conditions' => array('RoomGrade.clientId' => $this->Image->clientId),
                'order' => array('RoomGrade.roomGradeName')
            )
        );
        $this->set('roomGrades', $roomGrades);
    }

    /**
     *
     */
    public function slideshow()
    {
        if (!empty($this->data)) {
            if ($this->Image->ImageClient->saveAll($this->data['ImageClient'])) {
                $this->Session->setFlash('Your captions have been saved');
            } else {
                $this->Session->setFlash('Your captions could not be saved');
            }
        }

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

    /**
     *
     */
    public function delete_images()
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

    /**
     *
     */
    public function findNewImages()
    {
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

        $files = array();
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

    /**
     *
     */
    public function deduplicate()
    {
        $sql = 'SELECT DISTINCT clientId FROM (SELECT clientId,siteId,imageId,COUNT(clientImageId) AS c FROM imageClient WHERE imageTypeId=1 GROUP BY siteId,clientId,imageId ORDER BY COUNT(clientImageId) DESC) t1 WHERE c>1 ORDER BY clientId';
        $this->set('dupes', $this->Image->query($sql));
    }

    /**
     *
     */
    public function request()
    {
        $fromAddress = 'images@luxurylink.com';
        if (ISDEV) {
            $toAddress = 'devmail@luxurylink.com';
        } else {
            $toAddress = $this->Image->client['Client']['managerUsername'] . '@luxurylink.com';
        }

        $currentUser = $this->LdapAuth->user();
        $bccAddress = $currentUser['LdapUser']['samaccountname'] . '@luxurylink.com';

        $this->set('fromAddress', $fromAddress);
        $this->set('toAddress', $toAddress);

        if (isset($this->params['form']['msgSubject'])) {

            $headers = 'From: ' . $fromAddress . "\r\n";
            $headers .= 'Bcc: ' . $bccAddress . "\r\n";

            $msg = $this->params['form']['msgContent'];
            $msg .= "\n\n--\n" . $currentUser['LdapUser']['samaccountname'];

            mail($toAddress, $this->params['form']['msgSubject'], $msg, $headers);

            $this->Session->setFlash('Your image request has been sent.');
            $this->redirect('/clients/' . $this->Image->clientId . '/images/organize');

        } else {
            $requestType = (isset($this->params['url']['t'])) ? $this->params['url']['t'] : '';
            $priorities = array('H' => 'HIGH', 'M' => 'MEDIUM', 'L' => 'LOW');

            if ($requestType != 'C' && !array_key_exists($requestType, $priorities)) {
                echo 'unknown request type';
                exit;
            }

            $priorityLabel = (array_key_exists($requestType, $priorities)) ? $priorities[$requestType] : '***';

            $subject = ($requestType == 'C') ? 'Caption' : 'Image';
            $subject .= ' Request: ' . $priorityLabel . ' priority - ' . $this->Image->client['Client']['nameNormalized'] . ' (' . $this->Image->clientId . ')';

            $msg = 'Client Name: ' . $this->Image->client['Client']['nameNormalized'] . "\n";
            $msg .= 'Client ID: ' . $this->Image->clientId . "\n\n";
            $msg .= 'Priority Level: ' . $priorityLabel . "\n\n";
            $msg .= 'Client Gallery Status: (IE, Live XXL gallery, XL gallery, No Gallery)' . "\n\n";
            $msg .= 'Special Requests/Notes: (IE, exterior shot, pool shot, etc.)' . "\n";
            if ($requestType != 'C') {
                $msg .= "\n" . 'Photo requirements document:' . "\n";
                $msg .= 'http://www.luxurylink.com/images/photo_requirements.pdf' . "\n";
            }

            $msg .= "\n--\n";
            $msg .= 'You have received this message because you are the associated contact for ' . $this->Image->client['Client']['nameNormalized'] . ' in toolbox:' . "\n";
            $msg .= 'http://toolbox.luxurylink.com/clients/edit/' . $this->Image->clientId . "\n\n";
            $msg .= 'If you feel you have received this message in error, please forward this email to the correct AE/AM.';

            $this->set('msgSubject', $subject);
            $this->set('msgContent', $msg);
        }
    }
}
