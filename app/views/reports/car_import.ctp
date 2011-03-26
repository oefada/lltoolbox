<? $this->pageTitle = 'CAR Import Process (internal use)'?>

<span style="font-weight:bold; font-size:14px;"><?= sizeof($pendingFiles); ?> files ready to import (<?= sizeof($pendingRecords); ?> db records)</span>
<br/><br/>
<a href="/reports/car_import?|GO|=download">download new files from ftp</a>
<br/><br/>
<a href="/reports/car_import?|GO|=import">import pending files</a>
<br/><br/>
<?php

foreach ($messages as $msg) {
    echo $msg . '<br>';
}

?>

<br/><br/><br/><br/><br/><br/>

