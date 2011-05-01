<?php

require_once('process.php');


if (isset($_GET['project'])) {
	$project = $_GET['project'];
}
if (isset($_GET['scenario'])) {
	$scenario = $_GET['scenario'];
}

if (!isset($project) || !isset($scenario)) {
	echo json_encode(Array("status" => "error", "msg" => "invalid arguments"));
	die();
}

$stem_path = "/var/www/cs161/group2/stem";

$stem_cmd = $stem_path."/STEM -headless -log -uri platform:/resource/".$project."/scenarios/".$scenario.".scenario";

$stem = new Process($stem_cmd);

if ($stem->status()) {
	echo json_encode(Array("status" => "success", "pid" => $stem->getPid()));
} else {
	echo json_encode(Array("status" => "error", "msg" => "could not launch stem"));
}



?>