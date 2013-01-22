<?php
class MailingList extends AppModel {

	var $name = 'MailingList';
	var $useTable = 'mailingList';
	var $primaryKey = 'mailingListId';
	var $displayField = 'mailListName';

	var $hasMany = array('UserMailOptin' => array('foreignKey' => 'mailingListId'));

	public function getNewsletterData() {
		$arr[1][1]=array('name'=>'LL Newsletter');
		$arr[1][2]=array('name'=>'LL Partner/Access');
		$arr[1][3]=array('name'=>'LL Lifestyle');
		$arr[1][4]=array('name'=>'LL Insights');
		$arr[1][10]=array('name'=>'LL Inspiration');
		$arr[1][11]=array('name'=>'LL Insider');

		$arr[2][5]=array('name'=>'FG Newsletter and Insider');
		$arr[2][8]=array('name'=>'FG Connection/Partner');
		// There is a unique key 'uniqueMail' in userMailOptin. Thus, there cannot be both an LL and FG
		// newsletter subscription to these at the same time. It will either be one or the other. There is
		// no 'siteId' column in userMailOptin. The contactId points to the LL newsletter in Silverpop
		$arr[2][2]=array('name'=>'LL Partner');
		$arr[2][3]=array('name'=>'LL Lifestyle');
		$arr[2][1]=array('name'=>'LL Newsletter');

		$arr[3][6]=array('name'=>'VCOM Sale Reminder');
		$arr[3][7]=array('name'=>'VCOM Upcoming Preview');
		$arr[3][2]=array('name'=>'VCOM Partner/Select');
		$arr[3][3]=array('name'=>'VCOM Waitlist');
		return $arr;
	}
}

