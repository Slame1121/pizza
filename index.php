<?php
// Version
define('VERSION', '3.0.2.0');

// Configuration
if (is_file('config.php')) {
	require_once('config.php');
}

// Install
if (!defined('DIR_APPLICATION')) {
	header('Location: install/index.php');
	exit;
}
if (substr($_SERVER['HTTP_HOST'], 0, 4) === 'www.') {
	header("HTTP/1.1 301 Moved Permanently");
	header('Location: http'.(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 's':'').'://' . substr($_SERVER['HTTP_HOST'], 4).$_SERVER['REQUEST_URI']);
	exit;
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');

start('catalog');

