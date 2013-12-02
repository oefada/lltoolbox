<div>
	<?php
	echo $form->create(array('action' => 'add'));
	echo $form->input('url',array('label'=>'http://www.luxurylink.com/','style'=>'border-style: none; background: #eee'));
	$selectData=array('' => 'New blank template');
	foreach ($BlockPages as $BlockPage) {
		$selectData[$BlockPage['BlockPage']['blockPageId']] = $BlockPage['BlockPage']['url'];
	}
	echo $form->input('templatePageId', array('label' => 'Use template:', 'options' => $selectData));
	echo $form->submit('Create New Block');
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
                
                <? $viewLink = $BlockPage['BlockPage']['url'];
                   if (substr($viewLink, -2) == '-2') {
                        $viewLink = substr($viewLink, 0, -2);
                        $viewLink = 'http://www.luxurylink.co.uk' . $viewLink;
                   } else {
                        $viewLink = 'http://www.luxurylink.com' . $viewLink;
                   }
                ?>
                
                <?php echo $html->link('View Live', $viewLink, array('target' => '_blockView')); ?>
			</td>
			<td><?php echo $BlockPage['BlockPage']['created']; ?></td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
