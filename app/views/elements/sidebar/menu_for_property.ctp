<ul class="tree">
	<?php
	$clientId = $this->viewVars['clientId'];
	$currentLoaId = $client['Client']['currentLoaId'];
    $validPhotoUsers = array('jpadilla', 'jpawlowska', 'kgathany', 'dpen');
	?>
	<li><?=$html->link('INFO/ATTRIBUTES', array("controller" => 'clients', 'action' => 'edit', $clientId), array('update' => 'content-area', 'indicator' => 'spinner'))?></li>
	<li class="open">LOA <?=$html2->c($client['Loa']);?>
		<ul>
			<li style="margin-bottom:3px;"><?=$html->link('View All', "/clients/$clientId/loas", array('update' => 'content-area', 'indicator' => 'spinner') )?></li>
			<li style="margin-bottom:3px;"><?=$html->link('Current LOA Details', "/loas/edit/$currentLoaId", array('update' => 'content-area', 'indicator' => 'spinner'))?></li>
			<li style="margin-bottom:3px;"><?=$html->link('Current LOA Items', "/loas/items/$currentLoaId", array('update' => 'content-area', 'indicator' => 'spinner'))?></li>
			<li style="margin-bottom:3px;"><?=$html->link('Current LOA Data', "/loas/maintTracking/$currentLoaId", array('update' => 'content-area', 'indicator' => 'spinner'))?></li>
		</ul>
	</li>
	<li class="open">PACKAGE
		<ul>
			<li style="margin-bottom:3px;"><?=$html->link('List All', "/clients/$clientId/packages", array('update' => 'content-area', 'indicator' => 'spinner'))?></li>
			<li style="margin-bottom:3px;"><?=$html->link('Create', "/clients/$clientId/packages/add", array('update' => 'content-area', 'indicator' => 'spinner'))?></li>
			<li style="margin-bottom:3px;"><?=$html->link('Scheduling', "/scheduling/index/clientId:{$clientId}", array('update' => 'content-area', 'indicator' => 'spinner'))?></li>
		</ul>
	</li>
	<li style="margin-bottom:3px;"><?=$html->link('ROOM GRADE', "/clients/$clientId/room_grades", array('update' => 'content-area', 'indicator' => 'spinner'))?></li>
    <?php if (in_array($userDetails['samaccountname'], $validPhotoUsers) || in_array('Geeks', $userDetails['groups']) || in_array('creative', $userDetails['groups'])): ?>
        <li class="open">PHOTOS
            <ul>
                <li style="margin-bottom:3px;"><?php echo $html->link('Organize', '/clients/'.$clientId.'/images/organize', array('update' => 'content-area', 'indicator' => 'spinner')); ?></li>
                <li style="margin-bottom:3px;"><?php echo $html->link('Captions', '/clients/'.$clientId.'/images/captions', array('update' => 'content-area', 'indicator' => 'spinner')); ?></li>
                <li style="margin-bottom:3px;"><?php echo $html->link('PDP Slideshow', '/clients/'.$clientId.'/images/slideshow', array('update' => 'content-area', 'indicator' => 'spinner')); ?></li>
                <li style="margin-bottom:3px;"><?php echo $html->link('Delete', '/clients/'.$clientId.'/images/delete_images', array('update' => 'content-area', 'indicator' => 'spinner')); ?></li>
            </ul>
        </li>
    <?php endif; ?>
	<li>OFFERS</li>
	<li>REPORTS</li>
</ul>
