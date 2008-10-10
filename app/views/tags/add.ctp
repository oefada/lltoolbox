<?php
	echo $javascript->link('prototype');
	echo $javascript->link('scriptaculous/scriptaculous');
?>


<div class="tags form">
<?php echo $form->create('Tag');?>
	<fieldset>
 		<legend><?php __('Add Tag');?></legend>
	<?php
		echo $form->input('tagName');
		echo $form->input('Country');
		echo '<div id="statediv"></div>';
		echo '<div id="citydiv"></div>';
		echo '<hr />';
		echo $form->input('Coordinate');
		echo '<hr />';
		echo $form->input('Client');
	?>
	</fieldset>
	
<?php echo $form->end('Submit');?>
</div>

<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Tags', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Countries', true), array('controller'=> 'countries', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Country', true), array('controller'=> 'countries', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List States', true), array('controller'=> 'states', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New State', true), array('controller'=> 'states', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Cities', true), array('controller'=> 'cities', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New City', true), array('controller'=> 'cities', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Coordinates', true), array('controller'=> 'coordinates', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Coordinate', true), array('controller'=> 'coordinates', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Clients', true), array('controller'=> 'clients', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Client', true), array('controller'=> 'clients', 'action'=>'add')); ?> </li>
	</ul>
</div>

<?php 
echo $ajax->observeField( 
	'CountryCountry', 
    	array(
	        'url' => array( 'controller' => 'countries', 'action' => 'get_states' ),
        	'frequency' => 0.2,
        	'update' => 'statediv'
    	)
); 
?>