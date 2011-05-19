<?php
// This file kills all running STEM processes that were executed by the web user
// Additionally, all STEM output logs (csv) are cleared
require_once('settings.php');


exec("rm -r " . STEM_ROOT_PATH . "/workspace/1*/R*");
exec("pkill -u ". WEB_USER);

// Simple echo used when running file from console
// If this file is executed through the web, we won't ever see this message
// because the server will this running process before it prints the message
echo "killed all stem processes and cleaned logged data";
?>
