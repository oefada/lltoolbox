<?php
header("Content-type:application/vnd.ms-excel");
header("Content-disposition:attachment;filename=export".$this->pageTitle.".csv");
echo $content_for_layout;
?>