<ul id="menu">
	<li><?=$html->link('Billboard', Array('controller' => 'merchandising', 'action' => 'billboard'));?></li>
	<li><?=$html->link('Homepage Tabs', Array('controller' => 'merchandising', 'action' => 'hometabs'));?></li>
	<ul class="tree">
		<? if (isset($tabs) && is_array($tabs)) : ?>
			<? foreach ($tabs AS $tab) : ?>
			<li style="line-height: 13px; margin-bottom: 6px;"><?=$html->link($tab['tabName'], Array('controller' => 'merchandising', 'action' => 'hometabs?t=' . $tab['tabName']));?></li>
			<? endforeach; ?>
		<? endif; ?>
	</ul>
	<li><?=$html->link('Inspiration', Array('controller' => 'merchandising', 'action' => 'inspiration'));?></li>
	<li><?=$html->link('Featured Auction', Array('controller' => 'merchandising', 'action' => 'fauction'));?></li>
</ul>
