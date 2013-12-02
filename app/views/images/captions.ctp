<?php echo $html->css('images.css'); ?>
<?php $this->pageTitle = $client['Client']['name'].$html2->c($client['Client']['clientId'], 'Client Id:').'<br />'.$html2->c('manager: '.$client['Client']['managerUsername']); ?>

<div class="sitesTab">
   <?php foreach ($sites as $site => $siteName): ?>
		 <div id="<?php echo $site; ?>" class="<?php echo ($site == 'luxurylink') ? ' siteActive' :  ' siteInactive'; ?>"><?php __($sites[$site]); ?></div>
   <?php endforeach; ?>
</div>    

<?php foreach ($sites as $site => $siteName): ?>
   
   <? $urlPath = ($site == 2) ? 'www.familygetaway.com' : 'www.luxurylink.com'; ?>


   
   <?php $images = ${'images'.$site};
    //var_dump($images);
    ?>
    <div id="images-<?php echo $site; ?>" class="captionsmulti" <?php if ($site != 'luxurylink'): ?> style="display:none"<?php endif; ?>>
		<form method="post" id="captions">
			<h2><?php __('All Active Photos');?></h2>
            <? if ($site == 'luxurylink'){ ?>
            <?= $form->input('ImageClient.copy_to_family', array('label'=>' Copy  to Family','type'=>'checkbox'));?>
            <? } ?>
            <label></label>
			<h3>Add Caption (limit 55 characters, including spaces)</h3>
			<input type="submit" value="Save" class="save_changes" />

			<?php foreach($images as $image): 
						$clientImageId = $image['ImageClient']['clientImageId']; 
						$currentRoomGrade = (isset($image['ImageRoomGradeRel'])) ? $image['ImageRoomGradeRel']['roomGradeId'] : '';
					?>
					
					<div class="captionImage">
						<img src="http://<?= $urlPath; ?><?php echo $image['Image']['imagePath'] ?>" height="100" />
						<input type="hidden" name="data[ImageClient][<?php echo $clientImageId; ?>][clientImageId]" value="<?php echo $image['ImageClient']['clientImageId'] ?>" />
						<input type="hidden" name="data[ImageClient][<?php echo $clientImageId; ?>][imageId]" value="<?php echo $image['ImageClient']['imageId'] ?>" />
						<input type="hidden" name="data[ImageClient][<?php echo $clientImageId; ?>][currentRoomGrade]" value="<?php echo $currentRoomGrade; ?>" />
						<input type="hidden" name="data[ImageClient][<?php echo $clientImageId; ?>][currentCaption]" value="<?php echo str_replace('"', '', $image['ImageClient']['caption']); ?>" />
						<input type="text" maxlength="60" name="data[ImageClient][<?php echo $clientImageId; ?>][caption]" value="<?php echo $image['ImageClient']['caption'] ?>" />
						<div class="roomGrade">
							Room Grade:&nbsp;&nbsp;
							<select id="ImageRoomGradeId" name="data[ImageClient][<?php echo $clientImageId; ?>][roomGradeId]">
								<option></option>
								<?php foreach ($roomGrades as $room): ?>
									<?php $selected = ($currentRoomGrade == $room['RoomGrade']['roomGradeId']) ? ' selected' : '' ?>
									<option value="<?php echo $room['RoomGrade']['roomGradeId']; ?>"<?php echo $selected; ?>><?php echo $room['RoomGrade']['roomGradeName']; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
			<?php endforeach; ?>

			<input type="submit" value="Save" class="save_changes" />
		</form>
    </div>
  
    <script type="text/javascript">      
      Event.observe('<?php echo $site; ?>', 'click', function() { toggleSites('<?php echo $site; ?>'); });      
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
</script>