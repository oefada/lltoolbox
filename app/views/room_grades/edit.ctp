<form method="post">
    <h2>Edit Room Grade</h2>
    <?php echo $form->hidden('RoomGrade.roomGradeId', array('value' => $roomGrade['RoomGrade']['roomGradeId'])); ?>
    <?php echo $form->input('RoomGrade.roomGradeName', array('value' => $roomGrade['RoomGrade']['roomGradeName'])); ?>
	<?php echo $form->input('RoomGrade.roomLink', array('value' => $roomGrade['RoomGrade']['roomLink'])); ?>
    <?php echo $form->submit('Save'); ?>
</form>