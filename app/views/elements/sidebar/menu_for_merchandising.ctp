<ul id="menu">
	<li><?=$html->link('Billboard', Array('controller' => 'merchandising', 'action' => 'billboard'));?></li>
	<br/>
	<li><?=$html->link('Homepage Tabs', Array('controller' => 'merchandising', 'action' => 'hometabs'));?></li>
	<ul class="tree">
		<? if (isset($tabs) && is_array($tabs)) : ?>
			<? foreach ($tabs AS $tab) : ?>
			<li style="line-height: 13px; margin-bottom: 6px;"><?=$html->link(str_replace('<br />',"\n",$tab['tabName']), Array('controller' => 'merchandising', 'action' => 'hometabs?t=' . $tab['tabName']));?></li>
			<? endforeach; ?>
		<? endif; ?>
	</ul>
	<br/>
	<li><?=$html->link('Inspiration', Array('controller' => 'merchandising', 'action' => 'inspiration'));?></li>
	<br/>
	<li>Featured Auction</li>
	<ul class="tree">
		<li style="line-height: 13px; margin-bottom: 6px;"><?=$html->link('Homepage', Array('controller' => 'merchandising', 'action' => 'fauction'));?></li>
		<li style="line-height: 13px; margin-bottom: 6px;"><?=$html->link('Listing & Destination', Array('controller' => 'merchandising', 'action' => 'fauctionld'));?></li>
	</ul>
</ul>
