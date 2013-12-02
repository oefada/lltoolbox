<? $this->pageTitle = 'CAR Import Process (internal use)'?>

<span style="font-weight:bold; font-size:14px;"><?= $pendingFileCount; ?> files ready to import (<?= $pendingRecordCount; ?> db records)</span>
<br/><br/>
<a href="/reports/car_import?|GO|=download">download new files from ftp</a>
<br/><br/>
<a href="/reports/car_import?|GO|=import">import pending files</a>
<br/><br/>
<a href="/reports/car_import?|GO|=import&del=1">import pending files (delete skipped files)</a>
<br/><br/>
<?php

foreach ($messages as $msg) {
    echo $msg . '<br>';
}

?>

<br/><br/><br/><br/><br/><br/>

