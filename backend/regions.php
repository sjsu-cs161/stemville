<?php
/*
 * Takes the query 'project' and searches mongo database for an ISO country and
 * level code. Returns a JSON encoded list of regions identified by ISO 3166
 * codes. Fails if country and level gives is not in full_ISO_3166.json.
 */

require_once 'settings.php';

require_once('MongoClass.php');

if (isset($_GET['project'])) {
    $mc = new MongoClass();
    $rec = $mc->getRecord($_GET['project']);
    $map = $rec[maps][0];
    $COUNTRY = $map[country];
    $LEVEL = $map[level];
}
if (strlen($COUNTRY) != 3 || !isset($LEVEL)) {
    echo json_encode(Array("status" => "error", "msg" => "Query did not match any results"));
    die();
}

$list = json_decode(file_get_contents("../rsrc/json/full_ISO_3166.json"));
$output = Array();
$region_data = $list->$COUNTRY->$LEVEL;

for ($i=0; $i < count($region_data); $i++) {
    array_push($output, $region_data[$i]->code);
}
echo json_encode(Array("status" => "success", "data" => $output));

?>
