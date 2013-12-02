<?php
class RefundReason extends AppModel {

	var $name = 'RefundReason';
	var $useTable = 'refundReason';
	var $primaryKey = 'refundReasonId';
	var $displayField = 'refundReasonName';
	var $order = 'refundReasonName ASC';
}
?>