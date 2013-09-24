<?php
class JuPm_Servercli_Command_Builddb extends JuPm_ServerCli_CommandAbstract
{
    public function execute()
    {
        // get all packages
        $aPackages = array();
        
        $oDir = dir(JUPM_PACKAGES_FOLDER);

        while (false !== ($sEntry = $oDir->read())) {
            if ($sEntry == '.' || $sEntry == '..' || $sEntry == '.svn' || $sEntry == '.git') {
                continue;
            }

            if (preg_match('/^([a-z0-9\-]+)-([0-9\.]+).pkg$/', $sEntry)) {
                $aPackages[] = str_replace('.pkg', '', $sEntry);
            }
        }

        // build db
        $aDbPackages = array();
        $aDbPackageVersions = array();
        $aDbPackagesByName = array();
        $iDbPackageId = 1;

        echo "[*] Building serverdb" . "\n";

        foreach ($aPackages as $sPackage) {
            $sTmpPkgInfo = file_get_contents(JUPM_PACKAGES_FOLDER . '/' . $sPackage . '.pkg');

            if (!$sTmpPkgInfo) {
                echo "[!] $sPackage - error on reading .pkg file\n";

                continue;
            }

            $aTmpPkgInfoArray = @json_decode($sTmpPkgInfo, true);

            if (!is_array($aTmpPkgInfoArray)) {
                echo "[!] $sPackage - .pkg file is not valid json\n";
            }

            // already packageId for it?
            $sTmpRealName = $aTmpPkgInfoArray['name'];

            if (in_array($sTmpRealName, $aDbPackages)) {
                $iTmpPkgId = array_search($sTmpRealName, $aDbPackages);
            } else {
                $aDbPackages[$iDbPackageId] = $sTmpRealName;

                $aDbPackagesByName[$sTmpRealName] = $iDbPackageId;

                $iTmpPkgId = $iDbPackageId;

                $iDbPackageId++;
            }

            $aDbPackageVersions[$iTmpPkgId][$aTmpPkgInfoArray['version']] = $aTmpPkgInfoArray;

            echo "[*] " . $sTmpRealName . ' version ' . $aTmpPkgInfoArray['version'] . ' added' . "\n";
        }

        $sDbFileContent  = '';
        $sDbFileContent .= '<?php' . "\n";
        $sDbFileContent .= '$aDbPackages = ' . var_export($aDbPackages, true) . ';' . "\n";
        $sDbFileContent .= "\n";
        $sDbFileContent .= '$aDbPackageVersions = ' . var_export($aDbPackageVersions, true) . ';' . "\n";
        $sDbFileContent .= "\n";
        $sDbFileContent .= '$aDbPackagesByName = ' . var_export($aDbPackagesByName, true) . ';' . "\n";
        $sDbFileContent .= "\n";

        file_put_contents(JUPM_PACKAGES_DB, $sDbFileContent);

        echo "[*] Done\n";

        exit;
    }
}
