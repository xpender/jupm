<?php
class JuPm_Server_Controller
{
    private static $_oInstance;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function getInstance()
    {
        if (null === self::$_oInstance) {
            self::$_oInstance = new self();
        }

        return self::$_oInstance;
    }

    public function dispatch()
    {
        $sUri = $_SERVER['REQUEST_URI'];

        if (substr($sUri, 0, 1) == '/') {
            $sUri = substr($sUri, 1);
        }

        if (substr($sUri, -1) == '/') {
            $sUri = substr($sUri, 0, -1);
        }

        $aUri = explode('/', $sUri);

        $bJson = false;

        if (strtolower($aUri[0]) == 'json') {
            $bJson = true;

            array_shift($aUri);
        }

        if (strtolower($aUri[0]) == 'list') {
            new JuPm_Server_Command_List($bJson, $aUri);

            exit;
        } elseif (strtolower($aUri[0]) == 'info') {
            new JuPm_Server_Command_Info($bJson, $aUri);

            exit;
        } elseif (strtolower($aUri[0]) == 'get') {
            new JuPm_Server_Command_Get($bJson, $aUri);

            exit;
        }

        new JuPm_Server_Command_Help($bJson, $aUri);
    }
}
