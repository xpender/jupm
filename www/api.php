<?php
require 'bootstrap.php';

if (!file_exists(JUPM_PACKAGES_DB)) {
    echo 'ERROR - no packages db!';

    exit;
}

$oApiController = new JuPm_ServerApi_Controller();
$oApiController->dispatch();
