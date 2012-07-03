<?php
class JuPm_Server_PackagesDb
{
    private static $_oInstance;

    private $_aPackages;

    private $_aPackageVersions;

    private function __construct()
    {
        require JUPM_PACKAGES_DB;

        $this->_aPackages = $aDbPackages;

        $this->_aPackageVersions = $aDbPackageVersions;
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

    public function allPackages()
    {
        return $this->_aPackages;
    }

    public function getPackage($iPackageId)
    {
        if (!isset($this->_aPackages[$iPackageId])) {
            return false;
        }

        return $this->_aPackages[$iPackageId];
    }

    public function allPackageVersions($iPackageId)
    {
        if (!isset($this->_aPackages[$iPackageId])) {
            return false;
        }

        return $this->_aPackageVersions;
    }

    public function getPackageVersion($iPackageId, $iVersion)
    {
        if (!isset($this->_aPackageVersions[$iPackageId]) || !isset($this->_aPackageVersions[$iPackageId][$iVersion])) {
            return false;
        }

        return $this->_aPackageVersions[$iPackageId][$iVersion];
    }
}
