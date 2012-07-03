<?php
require 'bootstrap.php';

if (!file_exists(JUPM_PACKAGES_DB)) {
    echo 'ERROR - no packages db!';

    exit;
}

JuPm_Server_Controller::getInstance()->dispatch();
