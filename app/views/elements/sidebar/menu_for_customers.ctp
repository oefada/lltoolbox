<?php
if(isset($this->viewVars['userId'])):
$userId = $this->viewVars['userId']; 
?>
<ul id="menu" class="tree">
	<li><?=$html->link('Info/Attributes', array("controller" => 'users', 'action' => 'edit', $userId))?></li>
	<li><?=$html->link('Tickets', "/tickets/index/?searchUserId=$userId")?><?=$html2->c($numUserTickets)?></li>
	<li><?=$html->link('Bids', "/users/bids/$userId")?><?=$html2->c($numUserBids)?></li>
	<li>Offers</li>
	<li>Reports</li>
	<li><?=$html->link('Referrals Sent', "/users/referralssent/$userId")?></li>
	<li><?=$html->link('Referrals Received', "/users/referralsrecvd/$userId")?></li>
</ul>
<?php endif; ?>