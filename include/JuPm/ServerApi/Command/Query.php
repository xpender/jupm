<?php
class JuPm_ServerApi_Command_Query implements JuPm_ServerApi_CommandInterface
{
    public static function execute($aRequest)
    {
        $sPackage = $aRequest['name'];
        $sVersion = $aRequest['version'];

        $oPackagesDb = JuPm_Server_PackagesDb::getInstance();

        // get package id
        $iPackageId = $oPackagesDb->getPackageIdByName($sPackage);

        if (!$iPackageId) {
            return array(
                'result' => 'ERROR',
                'error' => 'invalid package'
                );
        }

        // find package version
        $aPackageVersions = $oPackagesDb->allPackageVersions($iPackageId);

        if (!isset($aPackageVersions[$sVersion])) {
            return array(
                'result' => 'ERROR',
                'error' => 'non existing version requested'
                );
        }

        $aVersion = $aPackageVersions[$sVersion];

        // return info
        $aReturn = array();
        $aReturn['result'] = 'OK';
        $aReturn['file'] = $aVersion['file'];
        $aReturn['md5'] = $aVersion['md5'];
        $aReturn['require'] = $aVersion['require'];
        $aReturn['contents'] = $aVersion['contents'];

        return $aReturn;
    }
}
