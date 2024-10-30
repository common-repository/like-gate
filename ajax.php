<?php

header('Cache-Control: no-cache');
header('Content-type: application/json');

$struct['status'] = 1;
$struct['data'] = 'test';

echo json_encode($struct);

?>
