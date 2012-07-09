<?php
class JuPm_Client_Command_Buildpkg extends JuPm_Client_CommandAbstract
{
    public function execute()
    {
        $sPackageJsonFile = CLIENT_CWD . '/package.json';

        if (!file_exists($sPackageJsonFile)) {
            echo "[!] package.json not found\n";

            exit;
        }

        $sPackageJson = @file_get_contents($sPackageJsonFile);

        if (!$sPackageJson) {
            echo "[!] error on reading package.json\n";

            exit;
        }

        $bPackageJsonValid = JuPm_Validator_PackageJson::validateString($sPackageJson, &$aPackageJsonErrors);

        if (!$bPackageJsonValid) {
            echo "[!] Errors found at package.json\n";

            foreach ($aPackageJsonErrors as $sError) {
                echo " - " . $sError . "\n";
            }

            exit;
        }

        $aPackageJson = json_decode($sPackageJson, true);

        if (!is_dir(CLIENT_CWD . '/out')) {
            mkdir(CLIENT_CWD . '/out');
        }
        
        $sPkgFileBase = $aPackageJson['name'] . '-' . $aPackageJson['version'];
        
        if (file_exists(CLIENT_CWD . '/out/' . $sPkgFileBase . '.pkg')) {
            echo '[!] Error, out/' . $sPkgFileBase . '.pkg already exists' . "\n";

            exit;
        }

        echo "[*] Building package" . "\n";
        echo " - name: " . $aPackageJson['name'] . "\n";
        echo " - version: " . $aPackageJson['version'] . "\n";
        
        // read files
        $aFiles = array();

        $this->_recursiveFileScan(CLIENT_CWD . '/src', $aFiles);

        // md5 files
        foreach ($aFiles as $sFile) {
            $aFileMd5s[$sFile] = md5_file(CLIENT_CWD . '/src/' . $sFile);
        }

        // tar src folder
        chdir(CLIENT_CWD . '/src');
        
        $sCommand = 'tar cf ' . CLIENT_CWD . '/out/' . $sPkgFileBase . '.tar' . ' *';

        exec($sCommand, $false, $iReturn);

        chdir(CLIENT_CWD);

        if ($iReturn !== 0) {
            echo "[!] Error on tar'ing\n";

            exit;
        }

        // generate hash
        $sTarMd5 = md5(CLIENT_CWD . '/out/' . $sPkgFileBase);

        // add m5 to package json
        $aPackageJson['file'] = $sPkgFileBase . '.tar';
        $aPackageJson['md5'] = $sTarMd5;

        // add files to package json
        $aPackageJson['contents'] = $aFileMd5s;

        // write pkg
        file_put_contents(CLIENT_CWD . '/out/' . $sPkgFileBase . '.pkg', json_encode($aPackageJson));
    }

    private function _recursiveFileScan($sPath, &$aFiles, $sExt = '')
    {
        // canonicalize path
        if (substr($sPath, -1) == '/') {
            $sPath = substr($sPath, 0, -1);
        }

        $oDir = dir($sPath);

        while (false !== ($sEntry = $oDir->read())) {
            if ($sEntry == '.' || $sEntry == '..' || $sEntry == '.git' || $sEntry == '.svn') {
                continue;
            }

            if (is_dir($sPath . '/' . $sEntry)) {
                $this->_recursiveFileScan($sPath . '/' . $sEntry, $aFiles, $sExt . $sEntry . '/');
            } else {
                $aFiles[] = $sExt . $sEntry;
            }
        }
    }
}
