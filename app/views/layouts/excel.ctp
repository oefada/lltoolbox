<?php 
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
echo $content_for_layout;
die; // If you don't die or exit here excel gets extra data that causes a file corruption
?> 
 