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
	<table style="width: auto;">
		<thead>
			<tr>
				<td>URL</td>
				<td>Actions</td>
				<td>Created</td>
			</tr>
		</thead>
		<tbody>
		<?php $altrow=false; foreach ($BlockPages as $BlockPage):
		?>
		<tr <?php echo ($altrow=!$altrow)?' class="altrow" ':''; ?>>
			<td style="font-size: 150%;"><?php echo $BlockPage['BlockPage']['url']; ?></td>
			<td>
				<b><?php echo $html->link('Edit', array('action' => 'edit', $BlockPage['BlockPage']['blockPageId'])); ?></b>
				|
				<?php echo $html->link('Revisions', array('action' => 'revisions', $BlockPage['BlockPage']['blockPageId'])); ?>
				|
				<?php echo $html->link('View Live', 'http://www.luxurylink.com' . $BlockPage['BlockPage']['url'], array('target' => '_blockView')); ?>
			</td>
			<td><?php echo $BlockPage['BlockPage']['created']; ?></td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
