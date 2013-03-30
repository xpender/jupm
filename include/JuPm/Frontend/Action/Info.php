<?php
/**
 * jupm
 *
 * @package net.xpender.jupm
 * @author Marko Kercmar <m.kercmar@bigpoint.net>
 */
class JuPm_Frontend_Action_Info extends JuPm_Frontend_ActionAbstract
{
    public function getName()
    {
        return 'info';
    }

    protected function _execute()
    {
        // get packages db
        $oPackagesDb = JuPm_Server_PackagesDb::getInstance();

        // package name
        $sPackage = $_REQUEST['package'];

        // get ID
        $iPackageId = $oPackagesDb->getPackageIdByName($sPackage);

        if (!$iPackageId) {
            Header('Location: /?action=list');

            exit;
        }

        // get versions..
        $aPackageVersions = $oPackagesDb->allPackageVersions($iPackageId);

        // sort by version
        uksort($aPackageVersions, array('JuPm_Helper_Version', 'sort'));

        // get latests version for info..
        $aTmp = $aPackageVersions;

        $aLatestVersion = array_shift($aTmp);

        unset($aTmp);

        // assign to template
        $this->_oTemplate->assign(
            'aLatestVersion',
            $aLatestVersion
            );

        $this->_oTemplate->assign(
            'aVersions',
            $aPackageVersions
            );

        // show template
        $this->_oTemplate->display(
            'info'
            );
    }
}
