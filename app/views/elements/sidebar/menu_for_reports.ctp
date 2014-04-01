<ul class="tree">
	<li><?=$html->link('Account Manager Client Report', $this->webroot . 'reports/mcr')?></li>
	<li><?=$html->link('Aging', $this->webroot . 'reports/aging')?></li>
	<li><?=$html->link('Auction Timeslot', $this->webroot . 'reports/auction_timeslot')?></li>
	<li><?=$html->link('Daily Sales', $this->webroot . 'reports/auction_winner')?></li>
	<li><?=$html->link('Bids', $this->webroot . 'reports/bids')?></li>
	<li><?=$html->link('Check-in Date', $this->webroot . 'reports/check_in_date')?></li>
	<li><?=$html->link('Client Activity Report (internal use)', $this->webroot . 'reports/car')?></li>
	<li><?=$html->link('Client Management', $this->webroot . 'reports/cmr')?></li>
	<li><?=$html->link('Experiments', $this->webroot . 'reports/experiments')?></li>
	<li><?=$html->link('Fixed Price', $this->webroot . 'reports/fixed_price')?></li>
    <li><?=$html->link('Fraud Check', $this->webroot . 'reports/fraud_check')?></li>
	<li><?=$html->link('Images Project', array('controller'=>'reports','action'=>'images_project')); ?></li>
	<li><?=$html->link('Inventory Management', $this->webroot . 'reports/imr')?></li>
	<li><?=$html->link('Invoice Report', $this->webroot . 'reports/invoice')?></li>
    <li><?=$html->link('Leads Report', $this->webroot . 'reports/leads')?></li>
    <li><?=$html->link('LOA Package In Report', $this->webroot . 'reports/booking_report')?></li>
	<li><?=$html->link('Merchandising Dashboard', $this->webroot . 'reports/merch')?></li>
	<li><?=$html->link('Offer Search', $this->webroot . 'reports/offer_search')?></li>
	<li><?=$html->link('Package Search', $this->webroot . 'reports/packages')?></li>
	<li><?=$html->link('Promotions Report', $this->webroot . 'reports/promotions')?></li>
	<li><?=$html->link('RAF Report', $this->webroot . 'reports/raf')?></li>
    <li><?=$html->link('Refund Requests', $this->webroot . 'refund_requests')?></li>
	<li><?=$html->link('Remit Packages', $this->webroot . 'reports/remit');?></li>
	<li><?=$html->link('Weekly Scorecard', $this->webroot . 'reports/weekly_scorecard')?></li>
</ul>
<h3>Other Tools</h3>
<ul class="tree">
	<li><?=$html->link('Client Newsletter Notifier', '/client_newsletter_notifier')?></li>
	<li><?=$html->link('Consolidated Report Batch', $this->webroot . 'reports/consolidated_report_batch')?></li>
</ul>
