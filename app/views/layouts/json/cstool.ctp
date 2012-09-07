<?php

if (isset($ajax_for_layout)) {
	echo json_encode($ajax_for_layout);
} else {
	echo $content_for_layout;
}
