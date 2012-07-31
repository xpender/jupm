<?php
class JuPm_Client_Command_ListUpgrades extends JuPm_Client_CommandAbstract
{
    public function execute()
    {
        // jupm.conf exists?
        $sJupmConfig = CLIENT_CWD . '/jupm.conf';

        if (!file_exists($sJupmConfig)) {
            echo "[!] jupm.conf missing\n";

            exit;
        }

        // get jump.conf content
        $sJupmConfigContent = file_get_contents($sJupmConfig);

        if (!$sJupmConfigContent) {
            echo "[!] jupm.conf not readable\n";

            exit;
        }

        $aJupmConfig = @json_decode($sJupmConfigContent, true);

        if (!is_array($aJupmConfig)) {
            echo "[!] jupm.conf invalid json\n";

            exit;
        }

        // Init repository clients
        $aRepos = array();

        $iRepo = 1;

        foreach ($aJupmConfig['repositorys'] as $sUrl) {
            $oRepo = new JuPm_Client_Repository(
                $sUrl
                );

            $aPing = $oRepo->ping();

            if ($aPing['result'] != 'OK') {
                echo "[!] Repo $sUrl doesn't respond correctly: " . $aPing['error'] . "\n";

                continue;
            }

            echo "[*] Repo $sUrl OKAY\n";

            $aRepos[$iRepo] = $oRepo;

            $iRepo++;
        }

        // Get requires
        $aRequires = $aJupmConfig['require'];

        // List repo packages..
        $aPackageToRepo = array();
        $aPackageVersions = array();

        foreach ($aRepos as $iRepoId => $oRepo) {
            $aRepoPkgs = $oRepo->listpkgs();

            if ($aRepoPkgs['result'] != 'OK') {
                echo "[!] Repo " . $oRepo->getUrl() . " error on listing packages\n";

                continue;
            }

            foreach ($aRepoPkgs['packages'] as $aRepoPkg) {
                $aPackageToRepo[$aRepoPkg['name']] = $iRepoId;
                $aPackageVersions[$aRepoPkg['name']] = $aRepoPkg['versions'];
            }
        }

        // depedency resolve..
        $aPkgQueryCache = array();

        $aToInstall = array();
        $bDepsResolved = false;

        $jx = 0;

        while (!$bDepsResolved) {
            foreach ($aRequires as $sPackage => $sVersion) {
                if (!isset($aPackageToRepo[$sPackage])) {
                    echo "[!] Package " . $sPackage . " not found\n";

                    exit;
                }

                $iRepoId = $aPackageToRepo[$sPackage];

                $oRepo = $aRepos[$iRepoId];

                $aQuery = $oRepo->query($sPackage, $sVersion);

                if ($aQuery['result'] != 'OK') {
                    echo "[!] Package " . $sPackage . ", Version " . $sVersion . " not available\n";

                    exit;
                }

                if (is_array($aQuery['require'])) {
                    foreach ($aQuery['require'] as $sReqPackage => $sReqVersion) {
                        $sReqVersion = str_replace(array('>=', '='), '', $sReqVersion); // TODO: We are not comparing versions and stuff..

                        if (!isset($aToInstall[$sReqPackage]) && !isset($aRequires[$sReqPackage])) {
                            $aRequires[$sReqPackage] = $sReqVersion;
                        }
                    }
                }

                $aPkgQueryCache[$sPackage][$sVersion] = $aQuery;

                $aToInstall[$sPackage] = $sVersion;

                unset($aRequires[$sPackage]);
            }

            if (count($aRequires) == 0) {
                $bDepsResolved = true;
            }
            
            $jx++;

            if ($jx >= 100) {
                echo '[!] Dep-Resolving: Looped 100 times, exiting now.. Something is mostly wrong!' . "\n";

                exit;
            }
        }

        // go through to be installed packages
        foreach ($aToInstall as $sPackage => $sVersion) {
            // get repo
            $iRepoId = $aPackageToRepo[$sPackage];

            // get versions
            $aVersions = $aPackageVersions[$sPackage];

            // sort by version
            usort($aVersions, array('JuPm_Helper_Version', 'sort'));

            // check
            if ($aVersions[0] !== $sVersion) {
                echo "[?] " . $sPackage . ": using version " . $sVersion . ", but latest is " . $aVersions[0] . "\n";
            } else {
                echo "[*] " . $sPackage . ": using latest version\n";
            }
        }
    }
}
