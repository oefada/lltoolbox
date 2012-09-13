<?php echo $html->css('images.css'); ?>
<?php $this->pageTitle = $client['Client']['name'].$html2->c($client['Client']['clientId'], 'Client Id:').'<br />'.$html2->c('manager: '.$client['Client']['managerUsername']); ?>
<form method="post" id="captions">
    <h2><?php __('All Active Photos');?></h2>
    <h3>Add Caption (limit 55 characters, including spaces)</h3>
    <input type="submit" value="Save" class="save_changes" />
    
    <?php foreach($images as $image): ?>
            <div class="captionImage">
                <img src="http://www.luxurylink.com/<?php echo $image['Image']['imagePath'] ?>" height="100" />                
                <div class="roomGrade">
                
                	<?php if ($image['Image']['caption']) { echo $image['Image']['caption'] . '<br /><br />'; } ?>

                    Room Grade:&nbsp;&nbsp;
                    <select id="ImageRoomGradeId" name="data[Image][<?php echo $image['Image']['imageId'] ?>][RoomGradeId]">
                        <option></option>
                        <?php foreach ($roomGrades as $room): ?>
                            <?php $selected = ($image['Image']['ImageRoomGradeRel'][0]['roomGradeId'] == $room['RoomGrade']['roomGradeId']) ? ' selected' : '' ?>
                            <option value="<?php echo $room['RoomGrade']['roomGradeId']; ?>"<?php echo $selected; ?>><?php echo $room['RoomGrade']['roomGradeName']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
    <?php endforeach; ?>
    
    <input type="submit" value="Save" class="save_changes" />
</form>