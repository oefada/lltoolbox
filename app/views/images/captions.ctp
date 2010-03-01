<?php echo $html->css('images.css'); ?>
<?php $this->pageTitle = $client['Client']['name'].$html2->c($client['Client']['clientId'], 'Client Id:'); ?>
<form method="post" id="captions">
    <h2><?php __('All Active Photos');?></h2>
    <h3>Add Caption (limit 55 characters, including spaces)</h3>
    <input type="submit" value="Save" class="save_changes" />
    
    <?php foreach($images as $image): ?>
            <div class="captionImage">
                <img src="<?php echo $image['Image']['imagePath'] ?>" height="100" />
                <input type="text" maxlength="60" name="data[Image][<?php echo $image['Image']['imageId'] ?>][caption]" value="<?php echo $image['Image']['caption']; ?>" />
                <div class="roomGrade">
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