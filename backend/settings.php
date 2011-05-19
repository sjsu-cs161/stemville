<?php

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/var/www/cs161/group2');
}
// Define root path of web project (stemville)
if (!defined('STEMVILLE_ROOT_PATH')) {
    define('STEMVILLE_ROOT_PATH', BASE_PATH . '/stemville');
}
// Define root path of STEM
if (!defined('STEM_ROOT_PATH')) {
    define('STEM_ROOT_PATH', BASE_PATH . '/stem');
}
// Define the user account used by the web server
if (!defined('WEB_USER')) {
	define('WEB_USER', 'www-data');
}

?>
