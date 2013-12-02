<?php

header('Content-type: application/json');
echo json_encode(array('Pong' => array('Time' => time())));
