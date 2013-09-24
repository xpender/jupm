<?php
class JuPm_Validator_PackageJson
{
    public static function validateString($sData, &$aErrors = array())
    {
        return self::_validate($sData, $aErrors);
    }

    public static function validateFile($sFilename, &$aErrors = array())
    {
        if (!file_exists($sFilename)) {
            $aErrors[] = 'File not found';

            return false;
        }

        $sData = file_get_contents($sFilename);

        if (!$sData) {
            $aErrors[] = 'File not readable';

            return false;
        }

        return self::validateString($sData, $aErrors);
    }

    private static function _validate($sData, &$aErrors)
    {
        $aArray = json_decode($sData, true);

        if (!is_array($aArray)) {
            $aErrors[] = 'Invalid json';

            return false;
        }

        $bError = false;

        if (!isset($aArray['name'])) {
            $aErrors[] = 'pkg name missing';

            $bError = true;
        }

        if (!isset($aArray['type'])) {
            $aErrors[] = 'pkg type missing';

            $bError = true;
        }

        if (!isset($aArray['version'])) {
            $aErrors[] = 'pkg version missing';

            $bError = true;
        }

        if (!isset($aArray['authors']) || count($aArray['authors']) < 1) {
            $aErrors[] = 'pkg authors missing';

            $bError = true;
        }

        if (!preg_match('/^[a-z0-9\-]+$/', $aArray['name'])) {
            $aErrors[] = 'pkg name contains invalid characters';

            $bError = true;
        }

        if (!preg_match('/^[0-9\.]+$/', $aArray['version'])) {
            $aErrors[] = 'pkg version contains invalid characters';

            $bError = true;
        }

        if ($bError) {
            return false;
        }

        return true;
    }
}
