<?php
if(isset($this->viewVars['userId'])):
$userId = $this->viewVars['userId']; 
?>
<ul>
	<li><?=$html->link('Info/Attributes', array("controller" => 'users', 'action' => 'edit', $userId))?></li>
	<li><?=$html->link('Tickets', "/users/tickets/$userId")?><?=$html2->c($numUserTickets)?></li>
	<li><?=$html->link('Bids', "/users/bids/$userId")?><?=$html2->c($numUserBids)?></li>
	<li>Offers</li>
	<li>Reports</li>
</ul>
<?php endif; ?>