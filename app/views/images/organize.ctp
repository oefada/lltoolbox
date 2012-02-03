<?php echo $html->css('images.css'); ?>
<?php $this->pageTitle = $client['Client']['name'].$html2->c($client['Client']['clientId'], 'Client Id:').'<br />'.$html2->c('manager: '.$client['Client']['managerUsername']); ?>
<div class="sitesTab">
   <?php foreach ($sites as $site => $siteName): ?>
         <div id="<?php echo $site; ?>" class="<?php echo ($site == $displayTab) ? ' siteActive' :  ' siteInactive'; ?>"><?php __($sites[$site]); ?></div>
   <?php endforeach; ?>
</div>
<?php foreach ($sites as $site => $siteName): ?>
   <?php
      $slideshowImages = ${'slideshowImages'.$site};
      $largeImages = ${'largeImages'.$site};
      $thumbnailImages = ${'thumbnailImages'.$site};
      $start_inactive = false;
   ?>
      <div id="images-<?php echo $site; ?>" class="organize" <?php if ($site != $displayTab): ?> style="display:none"<?php endif; ?>>
         <form method="post" id="organizeImages-<?php echo $site; ?>">
         <input type="hidden" name="data[saveSite]" value="<?php echo $site; ?>" />
            <div style="position:relative">
            <?php if (count($sites) > 1): ?>
               <span class="duplicateTo"><input type="checkbox" name="data[duplicateTo][<?php echo ($site == 'luxurylink') ? 'family' : 'luxurylink'; ?>]" /> Copy to <?php echo ($site == 'luxurylink') ? 'Family' : 'Luxury Link'; ?></span>
            <?php endif; ?>
            <span class="previewImages">
               <?php
                  if (in_array('luxurylink', $client['Client']['sites']) && $site == 'luxurylink') {
                      echo $html->link('<span>Preview on LuxuryLink</span>', "http://www.luxurylink.com/luxury-hotels/preview.html?clid={$client['Client']['clientId']}&preview=client", array('target' => '_blank', 'class' => 'button'), null, false);
                  }
                  if (in_array('family', $client['Client']['sites']) && $site == 'family') {
                      echo $html->link('<span>Preview on FamilyGetaway</span>', "http://www.familygetaway.com/luxury-hotels/preview.html?clid={$client['Client']['clientId']}&preview=client", array('target' => '_blank', 'class' => 'button'), null, false);
                  }
              ?>
           </span>
            </div>
          <input type="submit" value="Save" class="save_changes" />
         <?php $i=0; ?>
         <?php foreach ($slideshowImages as $ssImage):
                  if ($ssImage['ImageClient']['inactive'] == 1):
                        $start_inactive = $i;
                        if ($i > 0): ?>
                              </ul>
                            </div>
                        <?php else: ?>
                           <div class="organize slideshow">
                              <h3>Slideshow</h3>
                              <ul id="sortableSlideshow-<?php echo $site; ?>" class="sortableSS">
                              </ul>
                           </div>
                        <?php endif; ?>
                     <?php break;
                  elseif ($i == 0): ?>
                     <div class="organize slideshow">
                        <h3>Slideshow</h3>
                        <ul id="sortableSlideshow-<?php echo $site; ?>" class="sortableSS">
                  <?php endif; ?>
                  <li id="item_<?php echo $ssImage['ImageClient']['clientImageId'] ?>-<?php echo $site; ?>">
                     <img src="<?php echo $ssImage['Image']['imagePath']; ?>" style="vertical-align:bottom; max-height: 100px; max-width: 176px; margin: 1px;" alt="<?php echo $ssImage['Image']['caption']; ?>" />
                     <?php $fileArr = explode('/', $ssImage['Image']['imagePath']); ?>
                     <div class="filename"><?php echo end($fileArr); ?></div>
                     <input type="hidden" class="ss" name="data[ImageClient][<?php echo $ssImage['ImageClient']['clientImageId'] ?>][inactive]" value="<?php echo $ssImage['ImageClient']['inactive'] ?>" />
                     <input type="hidden" name="data[ImageClient][<?php echo $ssImage['ImageClient']['clientImageId'] ?>][imageId]" value="<?php echo $ssImage['ImageClient']['imageId'] ?>" />
                     <input type="hidden" name="data[ImageClient][<?php echo $ssImage['ImageClient']['clientImageId'] ?>][imageTypeId]" value="<?php echo $ssImage['ImageClient']['imageTypeId'] ?>" />
                  </li>
                  <?php if ($ssImage == $slideshowImages[count($slideshowImages)-1]): ?>
                              </ul>
                           </div>
                  <?php endif;
                        $i++;
                  ?>
         <?php endforeach; ?>
         
         <div class="organize slideshow inactive">
            <h3>Inactive</h3>
            <ul id="sortableInactive-<?php echo $site; ?>" class="sortableInactive">
               <?php if ($start_inactive !== false): ?>
                  <?php for ($i=$start_inactive; $i < count($slideshowImages); $i++): ?>
                     <li id="item_<?php echo $slideshowImages[$i]['ImageClient']['clientImageId'] ?>-<?php echo $site; ?>">
                          <img src="<?php echo $slideshowImages[$i]['Image']['imagePath']; ?>" height="100" alt="<?php echo $slideshowImages[$i]['Image']['caption']; ?>" />
                          <?php $fileArr = explode('/', $slideshowImages[$i]['Image']['imagePath']); ?>
                          <div class="filename"><?php echo end($fileArr); ?></div>
                          <input type="hidden" class="ss" name="data[ImageClient][<?php echo $slideshowImages[$i]['ImageClient']['clientImageId'] ?>][inactive]" value="<?php echo $slideshowImages[$i]['ImageClient']['inactive'] ?>" />
                          <input type="hidden" name="data[ImageClient][<?php echo $slideshowImages[$i]['ImageClient']['clientImageId'] ?>][imageId]" value="<?php echo $slideshowImages[$i]['ImageClient']['imageId'] ?>" />
                          <input type="hidden" name="data[ImageClient][<?php echo $slideshowImages[$i]['ImageClient']['clientImageId'] ?>][imageTypeId]" value="<?php echo $slideshowImages[$i]['ImageClient']['imageTypeId'] ?>" />
                     </li>
                  <?php endfor; ?>
               <?php else: ?>
                  <li class="empty">&nbsp;</li>
               <?php endif; ?>
            </ul>
         </div>
         <?php if (!empty($largeImages)): ?>
               <div class="organize large">
                  <h3>Large (225x169)</h3>
                  <ul id="largeImages-<?php echo $site ?>">
					 <?php
					     //loop through images and make sure only 1 is checked
						 $numChecked = 0;
						 foreach ($largeImages AS $i) {
						     if ($i['ImageClient']['inactive'] == 0) {
							     $numChecked++;
							 }
						 }
						 
						 // check first image and set rest to unchecked
						 if ($numChecked == 0 || $numChecked > 1) {
							 $c = 0;
						     foreach ($largeImages AS &$i) {
							     if ($c == 0) {
									$i['ImageClient']['inactive'] = 0;
									$c = 1;
								 } else {
									$i['ImageClient']['inactive'] = 1;
								 }
							 }
						 }
					 ?>
				  
				  
                     <?php foreach($largeImages as $lImage): ?>
                        <li>
                           <img src="<?php echo $lImage['Image']['imagePath']; ?>" height="75" alt="<?php echo $lImage['Image']['caption']; ?>" />
                           <input type="radio" class="lImage" name="data[ImageClient][<?php echo $lImage['ImageClient']['clientImageId'] ?>][inactive]" onclick="toggleRadio(this, 'largeImages-<?php echo $site ?>');" <?php if ($lImage['ImageClient']['inactive'] == 0) echo 'checked' ?> />
                           <?php $fileArr = explode('/', $lImage['Image']['imagePath']); ?>
                           <div class="filename large-filename"><?php echo end($fileArr); ?></div>
                           <input type="hidden" name="data[ImageClient][<?php echo $lImage['ImageClient']['clientImageId'] ?>][imageId]" value="<?php echo $lImage['ImageClient']['imageId'] ?>" />
                           <input type="hidden" name="data[ImageClient][<?php echo $lImage['ImageClient']['clientImageId'] ?>][imageTypeId]" value="<?php echo $lImage['ImageClient']['imageTypeId'] ?>" />
                        </li>
                     <?php endforeach; ?>
                  </ul>
               </div>
         <?php endif; ?>
         <?php if (!empty($thumbnailImages)): ?>
               <div class="organize thumbnails">
                  <h3>Thumbnail (70x64)</h3>
                  <ul id="thumbImages-<?php echo $site ?>">
					 <?php
					     //loop through images and make sure only 1 is checked
						 $numChecked = 0;
						 foreach ($thumbnailImages AS $i) {
						     if ($i['ImageClient']['inactive'] == 0) {
							     $numChecked++;
							 }
						 }
						 
						 // check first image and set rest to unchecked
						 if ($numChecked == 0 || $numChecked > 1) {
							 $c = 0;
						     foreach ($thumbnailImages AS &$i) {
							     if ($c == 0) {
									$i['ImageClient']['inactive'] = 0;
									$c = 1;
								 } else {
									$i['ImageClient']['inactive'] = 1;
								 }
							 }
						 }
					 ?>
					 
                     <?php foreach($thumbnailImages as $tImage): ?>
                        <li>
                           <img src="<?php echo $tImage['Image']['imagePath']; ?>" height="65" alt="<?php echo $tImage['Image']['caption']; ?>" />
                           <input type="radio" class="tImage" name="data[ImageClient][<?php echo $tImage['ImageClient']['clientImageId'] ?>][inactive]" onclick="toggleRadio(this, 'thumbImages-<?php echo $site ?>');"  <?php if ($tImage['ImageClient']['inactive'] == 0) echo 'checked' ?> />
                           <?php $fileArr = explode('/', $tImage['Image']['imagePath']); ?>
                           <div class="filename thumb-filename"><?php echo end($fileArr); ?></div>
                           <input type="hidden" name="data[ImageClient][<?php echo $tImage['ImageClient']['clientImageId'] ?>][imageId]" value="<?php echo $tImage['ImageClient']['imageId'] ?>" />
                           <input type="hidden" name="data[ImageClient][<?php echo $tImage['ImageClient']['clientImageId'] ?>][imageTypeId]" value="<?php echo $tImage['ImageClient']['imageTypeId'] ?>" />
                        </li>
                     <?php endforeach; ?>
                  </ul>
               </div>
         <?php endif; ?>
         <input type="submit" value="Save" class="save_changes" />
      </form>
   </div>


   <script type="text/javascript">
      Event.observe(window, 'load', function() {
                        Sortable.create('sortableSlideshow-<?php echo $site; ?>', {'overlap':'horizontal',
                                                                                   'constraint':false,
                                                                                   'ghosting':true,
                                                                                   'dropOnEmpty':true,
                                                                                   'containment':[$('sortableSlideshow-<?php echo $site; ?>'), $('sortableInactive-<?php echo $site; ?>')]
                                                                                  }
                                        );
                        Sortable.create('sortableInactive-<?php echo $site; ?>', {'overlap':'horizontal',
                                                                                  'constraint':false,
                                                                                  'ghosting':true,
                                                                                  'dropOnEmpty':true,
                                                                                  'containment':[$('sortableSlideshow-<?php echo $site; ?>'), $('sortableInactive-<?php echo $site; ?>')]
                                                                                  }
                                       );
      });
   
      Event.observe('organizeImages-<?php echo $site; ?>', 'submit', function() {
                        $('sortableInactive-<?php echo $site; ?>').adjacent('input.ss').each(function(hiddenField) {
                                                                  $(hiddenField).writeAttribute('value', 1);
                                                                 });
                        $('sortableSlideshow-<?php echo $site; ?>').adjacent('input.ss').each(function(hiddenField) {
                                                                  $(hiddenField).writeAttribute('value', 0);
                                                                 });
      });
      
      Event.observe('<?php echo $site; ?>', 'click', function() {
                        if ($('images-<?php echo $site; ?>').getStyle('display') == 'none') {
                           toggleSites('<?php echo $site; ?>');
                        }
      });
   </script>
<?php endforeach; ?>

<script type="text/javascript">
   function toggleSites(site) {
      var sitesArr = new Array();
      <?php foreach ($sites as $site => $siteName): ?>
         <?php echo "sitesArr.push('".$site."');"; ?>
      <?php endforeach; ?>
      sitesArr.each(function(siteTab) {
         $('images-'+siteTab).toggle();
         }
      );
      $$('div.siteActive').each(function(tab) { $(tab).removeClassName('siteActive').addClassName('siteInactive'); });
      $(site).removeClassName('siteInactive').addClassName('siteActive');
   }
   
   function toggleRadio(checkedRadio, ulElem) {
      switch(ulElem) {
         case 'largeImages':
            var radioClass='lImage';
            break;
         case 'thumbImages':
            var radioClass='tImage';
            break;
         default:
            var radioClass='';
      }
      $(ulElem).childElements().each(function(li) {
            $(li).adjacent('input.'+radioClass).each(function(radioElem) {
               if ($(radioElem) != $(checkedRadio) && radioElem.checked) {
                  radioElem.checked = false;
                  return true;
               }
            });
      });
   }
</script>