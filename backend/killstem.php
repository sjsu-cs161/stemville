<?php
// This file is used to kill a STEM process given a process id
require_once('process.php');

// get PID GET parameter
if (isset($_GET['pid'])) {
	$pid = $_GET['pid'];
}
// If no parameter was given, halt execution
if (!isset($pid)) {
	echo json_encode(Array("status" => "error", "msg" => "invalid argument"));
	die();
}

// Recreate STEM process
$stem = new Process();
$stem->setPid($pid);
// Attempt to stop the STEM process then let user know if it was successful
if ($stem->stop()) {
	echo json_encode(Array("status" => "success", "msg" => "STEM was stopped"));
} else {
	echo json_encode(Array("status" => "error", "msg" => "STEM could not be stopped"));
}

?>
