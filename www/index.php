<?php
/**
 * jupm
 *
 * @author Marko Kercmar <m.kercmar@bigpoint.net>
 */

 // include bootstrap
require 'bootstrap.php';

// check for packages db file
if (!file_exists(JUPM_PACKAGES_DB)) {
    echo 'ERROR - no packages db!';

    exit;
}

// le controller does the magic
$oFrontendController = new JuPm_Frontend_Controller();

$oFrontendController->dispatch(
    $_REQUEST
    );
