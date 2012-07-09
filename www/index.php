<?php
require 'bootstrap.php';

if (!file_exists(JUPM_PACKAGES_DB)) {
    echo 'ERROR - no packages db!';

    exit;
}

$oFrontendController = new JuPm_Frontend_Controller();
$oFrontendController->dispatch();
