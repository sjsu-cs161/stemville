<?php
// This file uses process.php to check whether stem is running
// It takes one parameter, pid, which contains a process id of stem

require_once('process.php');

// Read and store GET parameter
if (isset($_GET['pid'])) {
	$pid = $_GET['pid'];
}

// If no parameter was provided, output error and die
if (!isset($pid)) {
	echo json_encode(Array("status" => "error", "msg" => "invalid argument"));
	die();
}
// Recreate the STEM process based on given PID
$stem = new Process();
$stem->setPid($pid);

// If the process is running tell the user
// If it is NOT running, also tell the user so
if ($stem->status()) {
	echo json_encode(Array("status" => "success", "msg" => "STEM is currently running"));
} else {
	echo json_encode(Array("status" => "error", "msg" => "STEM is not running"));
}



?>