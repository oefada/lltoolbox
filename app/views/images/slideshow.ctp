<?php echo $html->css('images.css');
      echo $javascript->link('jquery/jquery');
      echo $javascript->link('jquery/jcarousel/lib/jquery.jcarousel');
?>
<link href="/js/jquery/jcarousel/lib/jquery.jcarousel.css" type="text/css" rel="stylesheet" />

<script type="text/javascript">
     jQuery.noConflict()(function() {
         jQuery().ready(function() {
            <?php foreach($clientSites as $site): ?>
                jQuery('#ssContainer-<?php echo $site; ?>').jcarousel({scroll:1,
                                                                       start:0,
                                                                       visible:1,
                                                                       wrap:'last',
                                                                       buttonNextHTML:'<div>&nbsp;&nbsp;&gt;&gt;</div>',
                                                                       buttonPrevHTML:'<div>&nbsp;&nbsp;&lt;&lt;</div>'}
                                                                       );
            <?php endforeach; ?>
            });
     });
 </script>


<?php $this->pageTitle = $client['Client']['name'].$html2->c($client['Client']['clientId'], 'Client Id:'); ?>

<div class="sitesTab">
   <?php foreach ($sites as $site => $siteName): ?>
         <div id="<?php echo $site; ?>" class="<?php echo ($site == 'luxurylink') ? ' siteActive' :  ' siteInactive'; ?>"><?php __($sites[$site]); ?></div>
   <?php endforeach; ?>
</div>

<?php foreach ($sites as $site => $siteName): ?>
   <?php $images = ${'images'.$site}; ?>
    <div id="images-<?php echo $site; ?>" class="organize" <?php if ($site != 'luxurylink'): ?> style="display:none"<?php endif; ?>>
        <form method="post">
            <h2><?php __('Slideshow');?></h2>
            <div class="captionSlideshow">
                <ul id="ssContainer-<?php echo $site ?>">
                <?php $i = 1; ?>
                <?php foreach($images as $image): ?>
                        <li id="image-<?php echo $image['ImageClient']['clientImageId'] ?>">
                            <input type="hidden" name="data[ImageClient][<?php echo $i-1 ?>][clientImageId]" value="<?php echo $image['ImageClient']['clientImageId'] ?>" />
                            <img src="<?php echo $image['Image']['imagePath'] ?>" height="250" />
                            <p><?php echo $i ?> of <?php echo count($images); ?></p>
                            <input type="text" name="data[ImageClient][<?php echo $i-1 ?>][caption]" value="<?php echo $image['ImageClient']['caption'] ?>" />
                        </li>
                        <?php $i++; ?>
                <?php endforeach; ?>
                </ul>
                <input type="submit" value="Save Changes" class="save_changes" />
            </div>
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
      
      jQuery.noConflict()(function() {
            <?php foreach($sites as $site => $siteName): ?>
                jQuery('#ssContainer-<?php echo $site; ?>').jcarousel({scroll:1,
                                                                       start:0,
                                                                       visible:1,
                                                                       wrap:'last',
                                                                       buttonNextHTML:'<div>&nbsp;&nbsp;&gt;&gt;</div>',
                                                                       buttonPrevHTML:'<div>&nbsp;&nbsp;&lt;&lt;</div>'}
                                                                       );
            <?php endforeach; ?>
     });
      
      $$('div.siteActive').each(function(tab) { $(tab).removeClassName('siteActive').addClassName('siteInactive'); });
      $(site).removeClassName('siteInactive').addClassName('siteActive');
   }
</script>