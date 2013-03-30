<?php
/**
 * jupm
 *
 * @package net.xpender.jupm
 * @author Marko Kercmar <m.kercmar@bigpoint.net>
 */
class JuPm_Frontend_Action_List extends JuPm_Frontend_ActionAbstract
{
    public function getName()
    {
        return 'list';
    }

    protected function _execute()
    {
        // get packages db
        $oPackagesDb = JuPm_Server_PackagesDb::getInstance();

        // new arary
        $aPackages = array();

        foreach ($oPackagesDb->allPackages() as $iPackageId => $sPackageName) {
            // get versions..
            $aPackageVersions = $oPackagesDb->allPackageVersions($iPackageId);

            // sort by version
            uksort($aPackageVersions, array('JuPm_Helper_Version', 'sort'));

            // get latests version for info..
            $aTmp = $aPackageVersions;

            $aLatestVersion = array_shift($aTmp);

            unset($aTmp);

            // save to array
            $aPackages[$iPackageId] = array(
                'id' => $iPackageId,
                'name' => $sPackageName,
                'latestVersion' => $aLatestVersion
                );
        }

        // assign to template
        $this->_oTemplate->assign(
            'aPackages',
            $aPackages
            );

        // show template
        $this->_oTemplate->display(
            'list'
            );
    }
}
