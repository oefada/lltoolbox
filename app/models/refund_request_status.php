<?php
class RefundRequestStatus extends AppModel {

	var $name = 'RefundRequestStatus';
	var $useTable = 'refundRequestStatus';
	var $primaryKey = 'RefundRequestStatusId';
	var $displayField = 'description';
	var $order = 'description ASC';
}
