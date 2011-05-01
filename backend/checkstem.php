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
$stem->setPid($pid)
if ($stem->status()) {
	echo json_encode(Array("status" => "success", "pid" => $stem->getPid()));
} else {
	echo json_encode(Array("status" => "error", "msg" => "could not launch stem"));
}



?>