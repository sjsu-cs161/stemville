<?php

if (!defined('BASE_PATH'))
    define('BASE_PATH', '/var/www/cs161/group2');

if (!defined('STEMVILLE_ROOT_PATH'))
    define('STEMVILLE_ROOT_PATH', BASE_PATH . '/stemville');


if (!defined('STEM_ROOT_PATH'))
    define('STEM_ROOT_PATH', BASE_PATH . '/stem');

#$STEM_COMMAND = 'cd ' . STEM_ROOT_PATH . ' && export DISPLAY=:0 && nohup '.$this->command.' > ' . ROOT_PATH . '/stemweblog/ 2>&1 & echo $!';
#$STEM_COMMAND = 'cd '.STEM_ROOT_PATH.' && export DISPLAY=:0 && nohup '.$this->command.' > /home/tseng/group2/stemweblog 2>&1 & echo $!';

?>
