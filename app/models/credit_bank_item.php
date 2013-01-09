<?php
class CreditBankItem extends AppModel {

	public $name = 'CreditBankItem';
	public $useTable = 'creditBankItem';
	public $primaryKey = 'creditBankItemId';
	

   var $belongsTo = array(
   						'CreditBankTransactionType' => array('foreignKey' => 'creditBankTransactionId'),
   						'CreditBankItemSource' => array('foreignKey' => 'creditBankItemSourceId')
					   );
}