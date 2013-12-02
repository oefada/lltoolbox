<?php
App::import("Vendor","Tokenizer",array('file' => "tokenizer.php"));
App::import('Model', 'UserPaymentSetting');
class TokenizerShell extends Shell
{
	private $tokenizer = null;
	private $logfile = 'tokenizer';
	private $UserPaymentSetting;

	public function initialize()
	{
		$this->UserPaymentSetting = new UserPaymentSetting();
		$this->UserPaymentSetting->setDataSource('vacationist');
		$this->tokenizer = new TokenizerHelper(
			TokenizerFactoryHelper::newTokenizerInstance('tokenex',
				array(
					'tokenExMerchantId'	=> Configure::read('tokenExMerchantId'),
					'tokenExWsdlURL'	=> Configure::read('tokenExWsdlURL')
				)
			)
		);
	}

	public function main()
	{
		$sql = "
			SELECT
				id, ccNumber, expMonth, expYear, ccToken
			FROM userPaymentSetting
			WHERE
				inactive=0
				AND ccToken IS NULL
		";
		$paymentRecords = $this->UserPaymentSetting->query($sql);

		foreach ($paymentRecords as $paymentRecord) {
			$id = $paymentRecord['userPaymentSetting']['id'];
			$expirationDate = '1212';
			$datetime = date('Y-m-d H:i:s');
			$ccNumberEncrypted = $paymentRecord['userPaymentSetting']['ccNumber'];
			$ccNumberDecrypted = $this->decryptCCNumVcom($ccNumberEncrypted);

			if (!Validation::cc($ccNumberDecrypted)) {
				continue;
			}

			try {
				$ccToken = $this->tokenizer->tokenizeCC($ccNumberDecrypted, $expirationDate);

				$sql = "
					UPDATE
						userPaymentSetting
					SET
						ccToken = ?,
						ccTokenModified = NOW(),
						ccTokenCreated = NOW()
					WHERE id = ?
				";
				$this->log("Tokenizing record id $id");
				$this->UserPaymentSetting->query($sql, array($ccToken,	$id));
			} catch (Exception $e) {
				$this->log($e->getMessage());
			}
		}
	}

	private function decryptCCNumVcom($ccNumberEncrypted)
	{
		$key = @md5('werdUpBhop');
		$ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		$iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, pack('H*', $ccNumberEncrypted), MCRYPT_MODE_ECB, $iv));
	}

	/**
	 * Utility method to log output
	 *
	 * @access	public
	 * @param	string message to log
	 */
	public function log($message)
	{
		parent::log($message, $this->logfile);
		echo date('Y-m-d H:i:s') . ' - ' . $message . "\n";
	}
}
