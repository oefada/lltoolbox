<?php echo $html->css('images.css');
      $this->pageTitle = $client['Client']['name'].$html2->c($client['Client']['clientId'], 'Client Id:'); 
?>

<form method="post">
    <h2><?php __('Delete Photos');?></h2>
    <div class="deleteImages"><input type="submit" value="Delete Selected" onclick="return confirm('Are you sure you would like to permanently delete the selected photos?');"  /></div>
    <?php $i=0; ?>
    <ul class="deleteImage">
    <?php foreach($images as $image): ?>
            <li>
                <input type="checkbox" name="data[ImageClient][<?php echo $i ?>][imageId]" value="<?php echo $image['ImageClient']['imageId'] ?>" />
                <img src="<?php echo $image['Image']['imagePath'] ?>" height="75" />
                <div class="siteStr">
                    <?php echo $image['ImageClient']['siteStr']; ?>
                </div>
            </li>
            <?php $i++; ?>
    <?php endforeach; ?>
    </ul>
    <div class="deleteImages"><input type="submit" value="Delete Selected" onclick="return confirm('Are you sure you would like to permanently delete the selected photos?');" /></div>
</form>