<?php


class CarDataImporterComponent extends Object {
    private $localdir = '/var/www/data/client-activity-report/';
    private $fileDownloadDaysBack = 10;
    private $conn;
    private $feedTypes = array('clientid47', 'destinationlist21', 'homelist49', 'searchlist50');
    private $echoMessages = false;

    private $messages = array();
    private $errors = array();

    function startup(&$controller) {
        $db = ConnectionManager::getInstance();
        $this->conn = $db->getDataSource('default');
    }

    // -------------------------
    // public functions
    // -------------------------
    public function importPendingFiles() {
        $importfiles = $this->pendingRecords();

        $this->addMessage(sizeof($importfiles) . ' files to import');

        $count = 0;
        foreach ($importfiles as $import) {
            $info = $this->feedInfoByFilename($import['carDataFile']['filename']);
            $info['carDataFileId'] = $import['carDataFile']['recordid'];

			if (!in_array($info['feed'], $this->feedTypes)) {
				$this->addMessage('unknown feed : ' . $info['carDataFileId'] . ' : ' . $info['filename']);
			} else {
				$result = $this->importDataFile($info);
				if ($result) { $count++; }
			}

        }
        $this->addMessage($count . ' files processed');
    }

    public function downloadNewFiles() {
        $ftp = $this->getFtpConnection();
        $newfiles = array();
        $ftp->chdir('omniture');
        $filenames = $ftp->nlist('.');
        foreach ($filenames as $fn) {
            $now = time();
            $modified = $ftp->mdtm($fn);
            if (($now - $modified) < ($this->fileDownloadDaysBack*86400)) {
                $newfiles[] = $fn;
            }
        }

        $this->addMessage('checking ' . sizeof($newfiles) . ' files');

        $countNew = 0;
        $countDone = 0;
        foreach ($newfiles as $fn) {
            // file already downloaded?
            if (!$this->isFileDownloaded($fn)) {
                $localfile = $this->localdir . $fn;
                $handle = fopen($localfile , 'w');
                $ftp->fget($handle, $fn, FTP_ASCII);
                fclose($handle);
                chmod($localfile, 0666);
                $this->conn->query("INSERT INTO reporting.carDataFile (filename, downloadDate, imported) VALUES ('" . $fn . "', NOW(), 0)");
            	$countNew++;
            } else {
				$countDone++;
            }
        }
        $ftp->close();

        $this->addMessage($countDone . ' files already processed');
        $this->addMessage($countNew . ' new files downloaded');

    }

    public function getPendingInfo() {
		$files = scandir($this->localdir);
		$pendingRecords = $this->pendingRecords();
		return array('files'=>$files, 'pendingRecords'=>$pendingRecords);
    }

    public function getMessages() {
        return $this->messages;
    }




    // -------------------------
    // private functions
    // -------------------------
    private function importDataFile($feedinfo) {

        // 1.  records already exist?
    	$tablename = $this->getFeedTable($feedinfo);
        $results = $this->conn->query("SELECT COUNT(*) AS nbr FROM " . $tablename . " WHERE reportingDate = '" . $feedinfo['date'] . "'");
        $nbr = $results[0][0]['nbr'];

        if ($nbr > 0) {
            $this->addMessage('skipping file : ' . $feedinfo['carDataFileId'] . ' : ' . $feedinfo['filename'] . ' : '  . $nbr . ' records found');
            return false;
        }

        // 2.  file to array
        $localfile = $this->localdir . $feedinfo['filename'];
        $lines = array();
        if (!file_exists($localfile)) {
            $this->addMessage('skipping file : ' . $feedinfo['carDataFileId'] . ' : ' . $feedinfo['filename'] . ' : file not found');
            return false;
        }
        $handle = fopen($localfile , 'r');
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if (is_array($data)) {
			    // only lines with data
			    if (intval(substr($data[0], 0, 1)) > 0)      {
			        $lines[] = $data;
			    }
			}
		}
		fclose($handle);

		$this->addMessage(sizeof($lines) . ' records in ' . $feedinfo['carDataFileId'] . ' : ' . $feedinfo['filename']);

        // 3.  insert lines
        foreach ($lines as $line) {
			$result = $this->insertFeedRecord($feedinfo, $line);
        }

        // 4.  results
        $importCount = $this->importedRecordCount($feedinfo);

		$this->addMessage($importCount . ' imported from ' . $feedinfo['carDataFileId'] . ' : ' . $feedinfo['filename']);

        // 5.  move file
        $localfiledone = $this->localdir . 'done/' . $feedinfo['filename'];
        rename ($localfile, $localfiledone);

        // 6.  update record
        $this->conn->query("UPDATE reporting.carDataFile SET imported = 1 WHERE filename = '" . $feedinfo['filename'] . "'");

        return true;

    }

    private function insertFeedRecord($feedinfo, $data) {
        $table = $this->getFeedTable($feedinfo);
        $columnsAndValues = $this->getFeedColumnsAndValues($feedinfo, $data);
    	if ($columnsAndValues) {
    	    $sql = 'INSERT INTO ' . $table . $columnsAndValues;
    	    $this->conn->query($sql);
        }
    }

    private function feedInfoByFilename($filename) {
        $info = array();
        $info['filename'] = $filename;
        $fn = substr($filename, 0, strlen($filename) - 4);
        $datesplit = explode('-', $fn);
        $info['date'] = date('Y-m-d', strtotime($datesplit[1]));
        $info['site'] = (stripos($datesplit[0], 'FamilyGetaway')) ? 'FG' : 'LL';
        $reportsplit = explode('report', strtolower($datesplit[0]));
        $info['feed'] = str_replace(' ', '', $reportsplit[0]);
        return $info;
    }

    private function getFeedTable($feedinfo) {
    	$table = '';
    	$feed = $feedinfo['feed'];
    	$site = $feedinfo['site'];
    	if ($feed == 'clientid47') {
    	    $table = ($site == 'FG') ? 'carDataFeed1Fg' : 'carDataFeed1';
    	} elseif ($feed == 'destinationlist21') {
    	    $table = ($site == 'FG') ? 'carDataFeedProp21Fg' : 'carDataFeedProp21';
    	} elseif ($feed == 'homelist49') {
    	    $table = ($site == 'FG') ? 'carDataFeedProp49Fg' : 'carDataFeedProp49';
    	} elseif ($feed == 'searchlist50') {
    	    $table = ($site == 'FG') ? 'carDataFeedProp50Fg' : 'carDataFeedProp50';
    	}
    	return 'reporting.' . $table;
    }

    private function getFeedColumnsAndValues($feedinfo, $data) {
    	$columns = '';
    	$values = '';
    	$feed = $feedinfo['feed'];
    	if ($feed == 'clientid47') {
    	    $client = $data[2];
    	    $columns = 'event6, event12, event1';
    	    $values = intval($data[3]) . ', ' . intval($data[5]) . ', ' . intval($data[7]);
    	} elseif ($feed == 'destinationlist21') {
    	    $client = $data[1];
    	    $columns = 'prop21';
    	    $values = intval($data[2]);
    	} elseif ($feed == 'homelist49') {
    	    $client = $data[1];
    	    $columns = 'prop49';
    	    $values = intval($data[2]);
    	} elseif ($feed == 'searchlist50') {
    	    $client = $data[1];
    	    $columns = 'prop50';
    	    $values = intval($data[2]);
    	}
    	$columns .= ', clientid, reportingDate, insertDateTime, carDataFileId';
    	$values .= ", " . $client . ", '" . $feedinfo['date'] . "', NOW(), " . $feedinfo['carDataFileId'];
    	if (intval($client) == 0) {
			$this->addMessage('bad client id : ' . $client . ' : ' . $feedinfo['carDataFileId'] . ' : ' . $feedinfo['filename'] . ' : ' . implode(',', $data));
    	    return false;
    	}
    	return ' (' . $columns . ') VALUES (' . $values . ')';
    }

    private function importedRecordCount($feedinfo) {
    	$tablename = $this->getFeedTable($feedinfo);
        $results = $this->conn->query("SELECT COUNT(*) AS nbr FROM " . $tablename . " WHERE carDataFileId = " . $feedinfo['carDataFileId']);
        return $results[0][0]['nbr'];
    }

    private function pendingRecords() {
        return $this->conn->query("SELECT * FROM reporting.carDataFile WHERE imported = 0");
    }

    private function isFileDownloaded($filename) {
        $results = $this->conn->query("SELECT * FROM reporting.carDataFile WHERE filename = '" . $filename . "'");
        if ($results) {
            return true;
        }
        return false;
    }

    private function isFileImported($filename) {
        $results = $this->conn->query("SELECT * FROM reporting.carDataFile WHERE filename = '" . $filename . "' AND imported = 1");
        if ($results) {
            return true;
        }
        return false;
    }

    private function getFtpConnection() {
        App::import('Vendor', 'Ftp', array('file' => 'ftp'.DS.'ftp.class.php'));
        $ftp = new Ftp;
        $ftp->connect('ftp.luxurylink.com');
        $ftp->login('llftp', 'llftp08!');
        return $ftp;
    }

    private function addMessage($msg) {
    	$m = date('Y-m-d H:i:s') . ' - ' . $msg;
        $this->messages[] = $m;
    }

}

?>
