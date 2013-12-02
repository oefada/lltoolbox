<ul>
	<?php foreach($dupes as $d):


	?>

	<li>
		<a href="/clients/<?php echo $d['t1']['clientId'];?>/images/delete_images"><?php echo $d['t1']['clientId'];?></a>
	</li>
	<?php endforeach;?>
</ul>