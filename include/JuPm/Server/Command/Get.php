<?php
class JuPm_Server_Command_Get extends JuPm_Server_CommandAbstract
{
    public function render()
    {
        $oPackagesDb = JuPm_Server_PackagesDb::getInstance();

        $iPackageId = (int)$this->_aParams[1];

        $sVersion = $this->_aParams[2];

        $aPackage = $oPackagesDb->getPackage($iPackageId);

        if (!$aPackage) {
            if ($bJson) {
                echo json_encode(
                    array(
                        'error' => 'invalid package'
                        )
                    );

                exit;
            } else {
                echo 'ERROR - invalid package';

                exit;
            }
        }

        $aPackageVersion = $oPackagesDb->getPackageVersion($iPackageId, $sVersion);

        if (!$aPackageVersion) {
            if ($bJson) {
                echo json_encode(
                    array(
                        'error' => 'invalid package'
                        )
                    );

                exit;
            } else {
                echo 'ERROR - invalid package';

                exit;
            }
        }

        Header('Content-type: application/x-tar');
        Header('Content-Disposition: attachment; filename="' . $aPackageVersion['file'] . '"');

        echo file_get_contents(JUPM_PACKAGES_FOLDER . '/' . $aPackageVersion['file']);
    }
}
