<?php
class JuPm_Client_Command_Deploy extends JuPm_Client_CommandAbstract
{
    public function execute()
    {
        $sJupmConfig = CLIENT_CWD . '/jupm.conf';

        if (!file_exists($sJupmConfig)) {
            echo "[!] jupm.conf missing\n";

            exit;
        }

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
