<?php
// quick and dirty
$country = $_GET['country'];
$level = intval($_GET['level']);

$MAP_SCALE = intval($_GET['scale']);
$INPUT_FILE = strtoupper($country)."_".$level."_MAP.xml";

require "map.php";
echo json_encode(Array("status" => "success", "data" => $mapArray));

?>