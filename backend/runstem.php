<?php
// This files attempts to execute a new headless stem process 
// based one two parameters: project name, and scenario 
require_once 'settings.php';

require_once('process.php');

// Retrieve GET parameters
if (isset($_GET['project'])) {
	$project = $_GET['project'];
}
if (isset($_GET['scenario'])) {
	$scenario = $_GET['scenario'];
}

// Halt execution if invalid parameters (missing)
if (!isset($project) || !isset($scenario)) {
	echo json_encode(Array("status" => "error", "msg" => "invalid arguments"));
	die();
}

// Assemble the STEM command
$stem_cmd = "./STEM -headless -log -uri platform:/resource/".$project."/scenarios/".$scenario.".scenario";

// Create the process and execute
$stem = new Process($stem_cmd);

// Return the status of the process
if ($stem->status()) {
	echo json_encode(Array("status" => "success", "pid" => $stem->getPid()));
} else {
	echo json_encode(Array("status" => "error", "msg" => "could not launch stem"));
}

?>
