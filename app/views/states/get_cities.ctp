<?php 
	echo $form->label('cities', 'Cities');
	echo $form->select('City.City', $cities, null, array('multiple' => true)); 
	
	
?>