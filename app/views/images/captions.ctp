<?php echo $html->css('images.css'); ?>
<?php $this->pageTitle = $client['Client']['name'].$html2->c($client['Client']['clientId'], 'Client Id:'); ?>

<form method="post" id="captions">
    <h2><?php __('All Active Photos');?></h2>
    <h3>Add Caption (limit 55 characters, including spaces)</h3>
    <input type="submit" value="Save" class="save_changes" />
    
    <?php foreach($images as $image): ?>
            <div class="captionImage">
                <img src="http://www.luxurylink.com<?php echo $image['Image']['imagePath'] ?>" height="100" />
                <input type="text" name="data[Image][<?php echo $image['Image']['imageId'] ?>][caption]" length="55" value="<?php echo $image['Image']['caption']; ?>" />
            </div>
    <?php endforeach; ?>
    
    <input type="submit" value="Save" class="save_changes" />
</form>