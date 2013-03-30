<?php
class JuPm_Frontend_Page_List extends JuPm_Frontend_PageAbstract
{
    public function display()
    {
        echo JuPm_Frontend_Template::head();
        
        $oPackagesDb = JuPm_Server_PackagesDb::getInstance();

        echo '<div class="main">' . "\n";
           
        echo '<table border="1">' . "\n";
        echo '<tr>' . "\n";
        echo '<th>Name</th>' . "\n";
        echo '<th>Latest version</th>' . "\n";
        echo '<th>Description</th>' . "\n";
        echo '</tr>' . "\n";
            
        foreach ($oPackagesDb->allPackages() as $iPackageId => $sPackageName) {
            // get versions..
            $aPackageVersions = $oPackagesDb->allPackageVersions($iPackageId);

            // sort by version
            uksort($aPackageVersions, array('JuPm_Helper_Version', 'sort'));

            // get latests version for info..
            $aTmp = $aPackageVersions;

            $aLatestVersion = array_shift($aTmp);

            unset($aTmp);

            echo '<tr>' . "\n";

            echo '<td><a href="/?action=info&package=' . $sPackageName . '">' . $sPackageName . '</a></td>' . "\n";
            echo '<td>' . $aLatestVersion['version'] . '</td>' . "\n";
            echo '<td>' . $aLatestVersion['description'] . '</td>' . "\n";
            
            echo '</tr>' . "\n";
        }

        echo '</table>' . "\n";

        echo '</div>' . "\n";

        echo JuPm_Frontend_Template::foot();
    }
}
