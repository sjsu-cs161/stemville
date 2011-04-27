<?php

require_once('MongoClass.php');

$mc = new MongoClass();

print_r($mc->listAllRecords());

echo "\n";

$rec = $mc->getRecord("1303886201473");
$map = $rec[maps][0];
$COUNTRY = $map[country];
$LEVEL = $map[level];

echo "record: " . json_encode($map) . "\n\n";
echo "country: " . $map[country] . "\n";
echo "level: " . $map[level] . "\n";

?>