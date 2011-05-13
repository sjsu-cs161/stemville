<?php

if (!defined('STEMVILLE_ROOT_PATH'))
    define('STEMVILLE_ROOT_PATH', dirname(dirname(__FILE__)) . '/');

if (!defined('BASE_PATH'))
    define('BASE_PATH', dirname(STEMVILLE_ROOT_PATH));

if (!defined('STEM_ROOT_PATH'))
    define('STEM_ROOT_PATH', BASE_PATH . '/stem/');

#$STEM_COMMAND = 'cd ' . STEM_ROOT_PATH . ' && export DISPLAY=:0 && nohup '.$this->command.' > ' . ROOT_PATH . '/stemweblog/ 2>&1 & echo $!';
$STEM_COMMAND = 'cd /home/tseng/group2/stem && export DISPLAY=:0 && nohup '.$this->command.' > /home/tseng/group2/stemweblog 2>&1 & echo $!';

?>
