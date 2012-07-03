<?php
// fix include path
$sProjectRoot = realpath(str_replace(basename(__FILE__), '', __FILE__) . '/../');

set_include_path('.:' . $sProjectRoot . '/include/');

// set constatns
define('JUPM_ROOT', $sProjectRoot);
define('JUPM_PACKAGES_FOLDER', $sProjectRoot . '/packages');
define('JUPM_PACKAGES_DB', JUPM_PACKAGES_FOLDER . '/packages.db');

// autoloader
require 'JuPm/Autoloader.php';

spl_autoload_register(array('JuPm_Autoloader', 'autoload'));
