<?php
class JuPm_Autoloader
{
    public static function autoload($sClassName)
    {
        require str_replace('_', '/', $sClassName) . '.php';
    }
}
