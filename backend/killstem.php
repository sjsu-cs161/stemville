<?php

require_once('process.php');


if (isset($_GET['pid'])) {
	$pid = $_GET['pid'];
}
if (!isset($pid)) {
	echo json_encode(Array("status" => "error", "msg" => "invalid argument"));
	die();
}
$stem = new Process();
$stem->setPid($pid);

if ($stem->stop()) {
	echo json_encode(Array("status" => "success", "msg" => "STEM was stopped"));
} else {
	echo json_encode(Array("status" => "error", "msg" => "STEM could not be stopped"));
}



?>
