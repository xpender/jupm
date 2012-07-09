<?php
class JuPm_Client_Command_Deploy extends JuPm_Client_CommandAbstract
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

        // check target
        $sTargetFolder = CLIENT_CWD . '/' . $aJupmConfig['target'];

        if (!is_dir($sTargetFolder)) {
            echo "[!] Target folder doesn't exist\n";

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

        foreach ($aRepos as $iRepoId => $oRepo) {
            $aRepoPkgs = $oRepo->listpkgs();

            if ($aRepoPkgs['result'] != 'OK') {
                echo "[!] Repo " . $oRepo->getUrl() . " error on listing packages\n";

                continue;
            }

            foreach ($aRepoPkgs['packages'] as $aRepoPkg) {
                $aPackagesToRepo[$aRepoPkg['name']] = $iRepoId;
            }
        }

        // depedency resolve..
        $aPkgQueryCache = array();

        $aToInstall = array();
        $bDepsResolved = false;

        $jx = 0;

        while (!$bDepsResolved) {
            foreach ($aRequires as $sPackage => $sVersion) {
                if (!isset($aPackagesToRepo[$sPackage])) {
                    var_dump($aRequires);
                    var_dump($aToInstall);
                    echo "[!] Package " . $sPackage . " not found\n";

                    exit;
                }

                $iRepoId = $aPackagesToRepo[$sPackage];

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

        var_dump($aToInstall);
        var_dump($aPkgQueryCache);

        exit;

        // TODO: Implement repositiory ping?

        // Get repositiory packages list
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $aJupmConfig['repository'] . '/json/list');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $r = curl_exec($ch);

        $j = json_decode($r, true);

        $aRemotePackages = $j['packages'];

        // deploy stuff..
        echo "[*] Deploying..\n";

        // Go through require's
        foreach ($aJupmConfig['require'] as $sPackage => $sVersion) {
            if (!in_array($sPackage, $aRemotePackages)) {
                echo "[!] Invalid package: $sPackage \n";

                continue;
            }

            $sVersion = str_replace(array('=', '>='), '', $sVersion);

            $iPackageId = (int)array_search($sPackage, $aRemotePackages);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $aJupmConfig['repository'] . '/json/get/' . $iPackageId . '/' . $sVersion . '/');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $r = curl_exec($ch);

            $sTmpFile = '/tmp/jupm.' . md5(time() . microtime(true) . uniqid() . rand(0, 100000));

            file_put_contents($sTmpFile, $r); 

            chdir($sTargetFolder);

            $sTarCommand = 'tar xvf ' . $sTmpFile;

            exec($sTarCommand);

            unlink($sTmpFile);

            echo "[*] " . $sPackage . " " . $sVersion . " deployed\n";
        }

        echo "[*] Done\n";
    }
}
