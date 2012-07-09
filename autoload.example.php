<?php
class JuPm_Vendor_Autoloader
{
    public static function autoload($sClassName)
    {
        $sFilePath = 'include/vendor/' . str_replace('_', PATH_SEPARATOR, $sClassName) . '.php';

        if (file_exists($sFilePath)) {
            require $sFilePath;
        }
    }
}

spl_autoload_register(array('JuPm_Vendor_Autoloader', 'autoload'));
