<?php
class JuPm_Client_LocalDb_Packages
{
    private $_aData;

    public function __construct()
    {
        $this->_load();
    }

    public function getLockPath()
    {
        return $this->getFilePath() . '.lock';
    }

    public function getFilePath()
    {
        return CLIENT_CWD . '/.jupm/packages.db';
    }

    private function _load()
    {
        if (file_exists($this->getLockPath())) {
            echo "[!] Lock " . $this->getLockPath() . " exists\n";

            exit;
        }

        touch($this->getLockPath());

        if (file_exists($this->getFilePath())) {
            $sTmp = file_get_contents($this->getFilePath());

            $aTmp = explode("\n", $sTmp);

            foreach ($aTmp as $x) {
                if (strlen(trim($x)) > 0) {
                    $y = explode(":", $x);

                    $this->_aData[$y[0]] = $y[1];
                }
            }
        } else {
            $this->_aData = array();
        }
    }

    private function _save()
    {
        $sContent = '';

        if (count($this->_aData) > 0) {
            foreach ($this->_aData as $sPackage => $sVersion) {
                $sContent .= $sPackage . ':' . $sVersion . "\n";
            }
        }

        file_put_contents($this->getFilePath(), $sContent);
    }

    public function exists($sPackage)
    {
        if (isset($this->_aData[$sPackage])) {
            return true;
        }

        return false;
    }

    public function add($sPackage, $sVersion)
    {
        $this->_aData[$sPackage] = $sVersion;
    }

    public function remove($sPackage)
    {
        unset($this->_aData[$sPackage]);
    }

    public function __destruct()
    {
        $this->_save();

        unlink($this->getLockPath());
    }

    public function all()
    {
        return $this->_aData;
    }
}
