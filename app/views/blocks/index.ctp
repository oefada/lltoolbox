<div>
	<h3>Open/create page:</h3>
	<?php
	echo $form->create(array('action' => 'add'));
	echo $form->input('url',array('label'=>'http://www.luxurylink.com/','style'=>'border-style: none; background: #eee'));
	echo $form->end();
	?>
</div>
<?php
$this->pageTitle = 'Blocks';
$this->set('hideSidebar', true);
?>
<div>
	<ul style="font-size: 180%;">
		<?php foreach ($BlockPages as $BlockPage):
		?>
		<li>
			<?php
			echo $html->link($BlockPage['BlockPage']['url'], array(
				'action' => 'edit',
				$BlockPage['BlockPage']['blockPageId'],
			));
			?>
		</li>
		<?php endforeach; ?>
	</ul>
</div>
