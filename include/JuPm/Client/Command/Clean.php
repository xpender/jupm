<?php
class JuPm_Client_Command_Clean extends JuPm_Client_CommandAbstract
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

        // local .jupm folder exists?
        if (!is_dir(CLIENT_CWD . '/.jupm/')) {
            echo "[!] No .jupm folder exists, mostly no packages are installed\n";

            exit;
        }

        // get local database
        $oLocalDbPackages = new JuPm_Client_LocalDb_Packages();
        $oLocalDbContents = new JuPm_Client_LocalDb_Contents();

        // go through packages
        foreach ($oLocalDbPackages->all() as $sPackage => $sVersion) {
            $aFiles = $oLocalDbContents->getByPackage($sPackage);

            foreach ($aFiles as $sFile) {
                unlink($sTargetFolder . '/' . $sFile);

                $oLocalDbContents->remove($sFile);
            }

            $oLocalDbPackages->remove($sPackage);

            echo "[*] " . $sPackage . " " . $sVersion . " removed\n";
        }

        echo "[*] Done\n";
    }
}
