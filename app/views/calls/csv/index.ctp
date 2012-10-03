<?php

$handle = fopen('php://output', 'a');

fputcsv($handle, array_keys($calls[0]['Call']), "\t");

foreach ($calls as $k => $row) {
	fputcsv($handle, $row['Call'], "\t");
}
