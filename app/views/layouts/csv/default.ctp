<?php
header("Content-type:application/vnd.ms-excel");
header("Content-disposition:attachment;filename=export".$this->pageTitle.".csv");
echo $content_for_layout;
die; // If you don't die or exit here excel gets extra data that causes a file corruption
?>