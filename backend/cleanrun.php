<?php
require_once('settings.php');


exec("rm -r " . STEM_ROOT_PATH . "/workspace/1*/R*");
exec("pkill -u www-data");

echo "killed all stem processes and cleaned logged data";
?>
