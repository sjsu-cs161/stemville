<?php

$ROOT_PATH = '/cs161/group2/';

define('STEMVILLE_ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . $ROOT_PATH . '/stemville/');
define('STEM_ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . $ROOT_PATH . '/stem/');

$STEM_COMMAND = 'cd /home/tseng/group2/stem && export DISPLAY=:0 && nohup '.$this->command.' > /home/tseng/group2/stemweblog 2>&1 & echo $!';

?>
