<?php 
	echo $form->label('states', 'States');
	echo $form->select('State.State', $states, null, array('multiple' => true)); 
	
	echo $ajax->observeField( 
		'StateState', 
	    	array(
		        'url' => array( 'controller' => 'states', 'action' => 'get_cities' ),
	        	'frequency' => 0.2,
	        	'update' => 'citydiv'
	    	)
	); 
	
?>