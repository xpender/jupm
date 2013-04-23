<?php
class JuPm_Client_Command_Buildpkg extends JuPm_Client_CommandAbstract
{
    public function execute()
    {
        // check for --force parameter
        $bForce = false;

        foreach ($this->_aCmdArguments as $sArgument) {
            if ($sArgument == '--force') {
                $bForce = true;
            }
        }

        // package.json exists in current CWD?
        $sPackageJsonFile = CLIENT_CWD . '/package.json';

        if (!file_exists($sPackageJsonFile)) {
            echo "[!] package.json not found\n";

            exit;
        }

        // package.json file readable?
        $sPackageJson = @file_get_contents($sPackageJsonFile);

        if (!$sPackageJson) {
            echo "[!] error on reading package.json\n";

            exit;
        }

        // package.json file valid?
        $bPackageJsonValid = JuPm_Validator_PackageJson::validateString($sPackageJson, $aPackageJsonErrors);

        if (!$bPackageJsonValid) {
            echo "[!] Errors found at package.json\n";

            foreach ($aPackageJsonErrors as $sError) {
                echo " - " . $sError . "\n";
            }

            exit;
        }

        // decode it an array
        $aPackageJson = json_decode($sPackageJson, true);

        // create out folder if non existant
        if (!is_dir(CLIENT_CWD . '/out')) {
            mkdir(CLIENT_CWD . '/out');
        }
        
        // define package file name
        $sPkgFileBase = $aPackageJson['name'] . '-' . $aPackageJson['version'];
        
        // if not forced, check if already built
        if (!$bForce) {
            if (file_exists(CLIENT_CWD . '/out/' . $sPkgFileBase . '.pkg')) {
                echo '[!] Error, out/' . $sPkgFileBase . '.pkg already exists' . "\n";

                exit;
            }
        }

        // start building..
        echo "[*] Building package" . "\n";
        echo " - name: " . $aPackageJson['name'] . "\n";
        echo " - version: " . $aPackageJson['version'] . "\n";
        
        // read files
        $aFiles = array();

        $this->_recursiveFileScan(CLIENT_CWD . '/src', $aFiles);

        sort($aFiles);

        // md5 files
        foreach ($aFiles as $sFile) {
            $aFileMd5s[$sFile] = md5_file(CLIENT_CWD . '/src/' . $sFile);
        }

        // tar src folder
        chdir(CLIENT_CWD . '/src');
        
        $sCommand = 'tar cf ' . CLIENT_CWD . '/out/' . $sPkgFileBase . '.tar' . ' ' . implode(' ', $aFiles);

        exec($sCommand, $false, $iReturn);

        chdir(CLIENT_CWD);

        // check tar return
        if ($iReturn !== 0) {
            echo "[!] Error on tar'ing\n";

            exit;
        }

        // generate hash
        $sTarMd5 = md5_file(CLIENT_CWD . '/out/' . $sPkgFileBase . '.tar');

        // add m5 to package json
        $aPackageJson['file'] = $sPkgFileBase . '.tar';
        $aPackageJson['md5'] = $sTarMd5;

        // add files to package json
        $aPackageJson['contents'] = $aFileMd5s;

        // fix: require could be empty, but should be set as empty array
        if (!isset($aPackageJson['require']) || !is_array($aPackageJson['require'])) {
            $aPackageJson['require'] = array();
        }

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
