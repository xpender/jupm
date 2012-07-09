<?php
class JuPm_Frontend_Page_Info extends JuPm_Frontend_PageAbstract
{
    public function display()
    {
        echo JuPm_Frontend_Template::head();
        
        $oPackagesDb = JuPm_Server_PackagesDb::getInstance();

        $sPackage = $_REQUEST['package'];

        $iPackageId = $oPackagesDb->getPackageIdByName($sPackage);

        if (!$iPackageId) {
            Header('Location: /?action=list');

            exit;
        }

        // back
        echo JuPm_Frontend_Template::back('/?action=list');

        // get versions..
        $aPackageVersions = $oPackagesDb->allPackageVersions($iPackageId);

        // TODO: Implement version sorter

        // get latests version for info..
        $aTmp = $aPackageVersions;

        $aLatestVersion = array_shift($aTmp);

        unset($aTmp);

        // show package info
        echo '<div class="main">' . "\n";

        echo '<table>' . "\n";

        echo '<tr><th>Name</th><td>' . $aLatestVersion['name'] . '</td></tr>' . "\n";
        echo '<tr><th>Description</th><td>' . $aLatestVersion['description'] . '</td></tr>' . "\n";
        echo '<tr><th>Latest version</th><td>' . $aLatestVersion['version'] . '</td></tr>' . "\n";
        echo '<tr><th>Authors</th><td>';

        $bNext = false;

        foreach ($aLatestVersion['authors'] as $aTmpAuthor) {
            if ($bNext) {
                echo '<br />';
            }

            echo '<a href="mailto:' . $aTmpAuthor['email'] . '">' . $aTmpAuthor['name'] . '</a>' . "\n";
        }

        echo '</td></tr>' . "\n";

        echo '</table>' . "\n";

        echo '</div>' . "\n";

        // list versions
        echo '<div class="main">' . "\n";

        echo 'All versions:<br />' . "\n";

        echo '<table>' . "\n";

        foreach ($aPackageVersions as $aVersion) {
            echo '<tr>' . "\n";

            echo '<td>' . $aVersion['version'] . '</td>' . "\n";
            echo '<td><a href="/?action=info&package=' . $aVersion['name'] . '&version=' . $aVersion['version'] . '">Info</a></td>';

            echo '</tr>' . "\n";
        }

        echo '</table>' . "\n";

        echo JuPm_Frontend_Template::foot();
    }
}
