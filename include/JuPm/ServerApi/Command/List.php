<?php
class JuPm_ServerApi_Command_List implements JuPm_ServerApi_CommandInterface
{
    public static function execute($aRequest)
    {
        $oPackagesDb = JuPm_Server_PackagesDb::getInstance();

        $aReturn = array();
        $aReturn['result'] = 'OK';
        $aReturn['packages'] = array();

        foreach ($oPackagesDb->allPackages() as $iPackageId => $sPackageName) {
            $aReturn['packages'][$iPackageId]['name'] = $sPackageName;
            $aReturn['packages'][$iPackageId]['versions'] = array();

            // get all versions
            $aPackageVersions = $oPackagesDb->allPackageVersions($iPackageId);

            // sort by version
            uksort($aPackageVersions, array('JuPm_Helper_Version', 'sort'));

            foreach ($aPackageVersions as $aPackageVersion) {
                $aReturn['packages'][$iPackageId]['versions'][] = $aPackageVersion['version'];
            }
        }

        return $aReturn;
    }
}
