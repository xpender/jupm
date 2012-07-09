<?php
class JuPm_ServerApi_Command_Download implements JuPm_ServerApi_CommandInterface
{
    public static function execute($aRequest)
    {
        $sFile = $aRequest['file'];

        // invalid characters?
        if (!preg_match('/^([a-z0-9\-\.]+)\.tar$/', $sFile)) {
            return array(
                'result' => 'ERROR',
                'error' => 'file not found'
                );
        }

        // file exists?
        $sFullPath = JUPM_PACKAGES_FOLDER . '/' . $sFile;

        if (!file_exists($sFullPath)) {
            return array(
                'result' => 'ERROR',
                'error' => 'file not found'
                );
        }

        // download..
        Header('Content-type: application/x-tar');
        Header('Content-Disposition: attachment; filename="' . $sFile . '"');

        echo file_get_contents($sFullPath);

        exit;
    }
}
