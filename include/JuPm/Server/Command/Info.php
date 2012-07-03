<?php
class JuPm_Server_Command_Info extends JuPm_Server_CommandAbstract
{
    public function render()
    {
        $oPackagesDb = JuPm_Server_PackagesDb::getInstance();

        $iPackageId = (int)$this->_aParams[1];

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

        $aPackageVersions = $oPackagesDb->allPackageVersions($iPackageId);

        if ($this->_bJson) {
            echo json_encode(
                array(
                    'packageId' => $iPackageId,
                    'versions' => array_keys($aPackageVersions)
                    )
                );
        } else {
            echo '<a href="/list/">back</a><br /><br />' . "\n";

            echo '<table border=1>';

            echo '<tr><th>Version</th><th>Requires</th><th>Authors</th><th>Contents</th></tr>' . "\n";

            foreach ($aPackageVersions as $aVersion) {
                echo '<tr>' . "\n";

                echo '<td>' . $aVersion['version'] . '</td>' . "\n";

                echo '<td>' . "\n";

                foreach ($aVersion['require'] as $sTmpPkgName => $sTmpPkgVersion) {
                    echo $sTmpPkgName . ' ' . $sTmpPkgVersion . '<br />';
                }

                echo '</td>' . "\n";

                echo '<td>' . "\n";
                
                foreach ($aVersion['authors'] as $aAuthor) {
                    echo '<a href="mailto:' . $aAuthor['email'] . '">' . $aAuthor['name'] . '</a><br />';
                }
                
                echo '</td>' . "\n";

                echo '<td><pre>' . "\n";

                foreach ($aVersion['contents'] as $sFile) {
                    echo $sFile . "\n";
                }

                echo '</pre></td>' . "\n";

                echo '<tr>' . "\n";
            }

            echo '</table>';
        }
    }
}
