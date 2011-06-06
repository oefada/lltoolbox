<?php
App::import('core', array('ConnectionManager'));
App::import('Vendor', 'amazon/s3');
class BookingReportShell extends Shell {
	private $db;
	private $logfile = 'booking_report';
	private $errors = array();
	
	private $s3_accessKey = 'AWS_ACCESS_KEY';
	private $s3_secretKey = 'AWS_SECRET_KEY';
	private $s3_bucket = 'AWS_S3_BUCKET';
	private $filepath = '/tmp/';
	
	private $reportHeaders = array(
		'account_id',
		'site_id',
		'event_timestamp',
		'event_id',
		'event_type',
		'event_value',
		'member_id'
	);
	
	public function initialize() {
		$this->log('Process Started.', $this->logfile);
		$this->db =& ConnectionManager::getDataSource('shared');
	}

	public function __destruct() {
		$this->log('Process Completed.', $this->logfile);
	}
	
	public function main() {
		$this->s3_accessKey = isset($this->params['a']) ? $this->params['a'] : null;
		$this->s3_secretKey = isset($this->params['p']) ? $this->params['p'] : null;
		$this->s3_bucket = isset($this->params['b']) ? $this->params['b'] : null;
		
		$daysBack = (isset($this->params['n']) AND intval($this->params['n']) !==0) ? intval($this->params['n']) : 1;
		$offerEndDate = date('Y-m-d', strtotime("-$daysBack days"));
		$reportData = '';
		$filename = date('Ymd-His') . '.tsv';
		$fp = '';
		$status = FALSE;
		$s3 = '';
		$winningBids = array_merge(
			$this->getWinningBids('luxurylink', $offerEndDate),
			$this->getWinningBids('familygetaway', $offerEndDate)
		);
		
		$this->log(sizeof($winningBids) . ' total winning bids for ' . $offerEndDate . '.', $this->logfile);
		if (sizeof($winningBids) > 0) {
			$this->log('Building tsv data.', $this->logfile);
			$reportData = $this->buildTsvFile($winningBids);
			
			if (isset($this->params['dryrun'])) {
				$this->out($reportData);
			} else {
				$this->log("Writing tsv file to {$this->filepath}$filename.", $this->logfile);
				$fp = fopen($this->filepath . $filename, 'w');
				$status = fwrite($fp, $reportData); 
				fclose($fp);
			
				if ($status !== FALSE) {
					$this->log("Uploading {$this->filepath}$filename to S3 bucket {$this->s3_bucket}.", $this->logfile);
					$s3 = new S3($this->s3_accessKey, $this->s3_secretKey);
					$status = $s3->putObjectFile($this->filepath . $filename, $this->s3_bucket, $filename, S3::ACL_PUBLIC_READ);
				
					if ($status !== TRUE) {
						$message = "Error uploading tsv file to S3 bucket {$this->s3_bucket}";
						$this->errors[] = $message;
						$this->log($message, $this->logfile);
					}

					$this->log("Deleting file {$this->filepath}$filename", $this->logfile);
					$status = unlink($this->filepath . $filename);
				} else {
					$message = "Error writing tsv file to {$this->filepath}$filename.";
					$this->errors[] = $message;
					$this->log($message, $this->logfile);				
				}
				if (!empty($this->errors)) {
					$this->sendEmailNotification($this->errors);
				}				
			}			
		}
	}
	
	private function getWinningBids($site, $date) {
		$data_to_return = array();
		$siteId = ($site == 'luxurylink') ? 1 : 2;
		$offerTable = ($siteId == 1) ? 'offerLuxuryLink' : 'offerFamily';
		$sql = "
			SELECT 
				'$site' as account_id,
				b.siteId as site_id,
				UNIX_TIMESTAMP(b.bidDateTime) as event_timestamp,
				b.offerId as event_id,
				'conversion' as event_type,
				b.bidAmount as event_value,	
				b.userId as member_id	
			FROM
				bid b,
				$offerTable o
			WHERE
				b.winningBid = 1
				AND b.siteId = $siteId
				AND b.offerId = o.offerId
				AND o.endDate >= '$date 00:00:00'
				AND o.endDate <= '$date 23:59:59'
			ORDER BY b.bidDateTime
		";
		
		$results = $this->db->query($sql);
		if ($results !== FALSE) {
			foreach($this->db->query($sql) as $data) {
				$data_to_return[] = array(
					'account_id' => $data[0]['account_id'],
					'site_id' => $data['b']['site_id'],
					'event_timestamp' => $data[0]['event_timestamp'],
					'event_id' => $data['b']['event_id'],
					'event_type' => $data[0]['event_type'],
					'event_value' => $data['b']['event_value'],
					'member_id' => $data['b']['member_id'],
				);
			}
		}
		
		return $data_to_return;
	}
	
	private function buildTsvFile($report_data) {
		$data_to_return = '';
		
		foreach($this->reportHeaders as $header) {
			$data_to_return .= $header . "\t";
		}
		$data_to_return = trim($data_to_return);
		
		foreach($report_data as $data) {
			$data_to_return .= "\r\n" . implode("\t", $data);
		}
		$data_to_return .= "\r\n";
		
		return $data_to_return;
	}
	
	private function sendEmailNotification($messages) {
		$emailTo = 'mclifford@luxurylink.com';
		$emailSubject = "Error encountered in Toolbox shell - booking_report.php";
		$emailHeaders = "From: LuxuryLink.com DevMail<devmail@luxurylink.com>\r\n";
		$emailBody = "While generating the booking report for Convertro I encountered the following error(s):\r\n\r\n";
		foreach($messages as $message) {
			$emailBody .= $message . "\r\n";
		}
		@mail($emailTo, $emailSubject, $emailBody, $emailHeaders);		
	}
}
?>