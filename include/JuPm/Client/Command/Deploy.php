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

        // local .jupm folder exists?
        if (!is_dir(CLIENT_CWD . '/.jupm/')) {
            @mkdir(CLIENT_CWD . '/.jupm/');
        }
        
        if (!is_dir(CLIENT_CWD . '/.jupm/')) {
            echo "[!] Error creating .jupm folder\n";

            exit;
        }

        // get local database
        $oLocalDbPackages = new JuPm_Client_LocalDb_Packages();
        $oLocalDbContents = new JuPm_Client_LocalDb_Contents();

        // install stuff
        foreach ($aToInstall as $sPackage => $sVersion) {
            if ($oLocalDbPackages->exists($sPackage)) {
                echo "[*] " . $sPackage . " already installed\n";

                continue;
            }

            $aPkgQuery = $aPkgQueryCache[$sPackage][$sVersion];

            // check file integrity
            foreach ($aPkgQuery['contents'] as $sFile => $sMd5) {
                if ($oLocalDbContents->exists($sFile)) {
                    echo "[!!!] " . $sFile . " already exists?\n";

                    exit;
                }
            }

            // download package..
            $sTmpFile = '/tmp/jupm.' . md5($aPkgQuery['file'] . time() . microtime(true) . uniqid() . mt_rand(0, 10000));

            // get repo client
            $iRepoId = $aPackagesToRepo[$sPackage];

            $oRepo = $aRepos[$iRepoId];

            // download
            $oRepo->download($aPkgQuery['file'], $sTmpFile);

            // md5 check
            $sLocalMd5 = md5_file($sTmpFile);
            $sRemoteMd5 = $aPkgQuery['md5'];

            if ($sLocalMd5 !== $sRemoteMd5) {
                echo "[!] MD5 Check failed for " . $aPkgQuery['file'] . " ($sLocalMd5 !== $sRemoteMd5)\n";

                exit;
            }

            // extract
            $sOldCwd = getcwd();

            chdir($sTargetFolder);

            $sTarCommand = 'tar xf ' . $sTmpFile . ' -C ' . $sTargetFolder;

            echo "[?] $sTarCommand\n";

            exec($sTarCommand);

            chdir($sOldCwd);

            // remove downloaded file
            unlink($sTmpFile);
            
            // register package
            $oLocalDbPackages->add($sPackage, $sVersion);

            // register contents
            foreach ($aPkgQuery['contents'] as $sFile => $sMd5) {
                $oLocalDbContents->add($sFile, $sPackage);
            }

            echo "[*] " . $sPackage . " " . $sVersion . " installed\n";
        }

        echo "[*] Done\n";
    }
}
