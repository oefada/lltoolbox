<?php
$this->pageTitle = 'Credit Bank';
$this->set('hideSidebar', true);
?>


<div class="clients index">
<?php echo $this->renderElement('ajax_paginator', array('showCount' => true)); ?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('creditBankId');?></th>
	<th><?php echo $paginator->sort('userId');?></th>
	<th><?php echo $paginator->sort('balance');?></th>
	<th><?php echo $paginator->sort('dateCreated');?></th>
</tr>

<?php
$i = 0;
foreach ($banks as $bank):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>

		<td>
			<?php echo $html->link(__($bank['CreditBank']['creditBankId'], true), array('action'=>'edit', $bank['CreditBank']['creditBankId'])); ?>
		</td>
		<td>
			<?php echo $html->link(__($bank['CreditBank']['userId'], true), '/users/edit/' . $bank['CreditBank']['userId']) . " - " . $bank['User']['firstName'] . " " . $bank['User']['lastName']; ?>
		</td>
		<td>$<?php echo number_format($bank['0']['balance'], 2); ?></td>
		<td><?php echo date('M d, Y - h:i a', strtotime($bank['CreditBank']['dateCreated'])); ?></td>
	</tr>
	
<? endforeach; ?>
</div>


</table>
<?php echo $this->renderElement('ajax_paginator', array('showCount' => true)); ?>