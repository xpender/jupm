<?php
$sProjectRoot = realpath(str_replace(basename(__FILE__), '', __FILE__) . '/../');

set_include_path('.:' . $sProjectRoot . '/include/');

define('JUPM_ROOT', $sProjectRoot);

require 'JuPm/Autoloader.php';

spl_autoload_register(array('JuPm_Autoloader', 'autoload'));
