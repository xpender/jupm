<?php
class JuPm_Website
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
        echo 'Do some nice things..';
    }
}
