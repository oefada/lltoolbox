<?php
$this->pageTitle = $client['Client']['name'].$html2->c($clientId, 'Client Id:');
?>
<?php echo $html->css('images.css'); ?>
<h2 class="title">Add/Edit Available Room Grades</h2>

<div id="roomGrades">
    <?php foreach ($roomGrades as $room): ?>
        <div class="roomGrade">
            <h3><?php echo $room['RoomGrade']['roomGradeName']; ?></h3><span class="roomGradeActions"><?php echo $html->link('Edit', '/clients/'.$clientId.'/room_grades/edit/'.$room['RoomGrade']['roomGradeId'], array('title' => 'Edit Room Grade', 'onclick' => 'Modalbox.show(this.href, {title: this.title, width:900});return false','complete' => 'closeModalbox()'), null, false); ?> | <?php echo $html->link('Delete', '/clients/'.$clientId.'/room_grades/delete/'.$room['RoomGrade']['roomGradeId'], array(), 'Are you sure you want to delete the '.$room['RoomGrade']['roomGradeName'].' room grade?'); ?></a></span>
        </div>
            <?php if (!empty($room['ImageRoomGradeRel'])): ?>
                <div class="roomGradeImages">
                    <p>Associated Photos:</p>
                    <?php foreach($room['ImageRoomGradeRel'] as $image): ?>
                            <img src="http://www.luxurylink.com<?php echo $image['Image']['imagePath']; ?>" height="100" /><br />
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
    <?php endforeach; ?>
    
    <form method="post">
        <?php echo $form->input('RoomGrade.roomGradeName', array('label' => false)); ?>&nbsp;&nbsp;<input type="submit" value="Add" />
    </form>
    
</div>