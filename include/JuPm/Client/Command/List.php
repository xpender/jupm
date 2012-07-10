<?php
class JuPm_Client_Command_List extends JuPm_Client_CommandAbstract
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

        // Get packages list..
        foreach ($aRepos as $oRepo) {
            echo '[*] Packages on ' . $oRepo->getUrl() . "\n";

            $aList = $oRepo->listpkgs();

            if ($aList['result'] != 'OK') {
                echo '[!!!] Error on list command' . "\n";

                continue;
            }

            foreach ($aList['packages'] as $aPackage) {
                echo '- ' . $aPackage['name'] . ' Versions: ';

                $bNext = false;

                foreach ($aPackage['versions'] as $sVersion) {
                    if ($bNext) {
                        echo ', ';
                    }

                    echo $sVersion;

                    $bNext = true;
                }

                echo "\n";
            }
        }
    }
}
