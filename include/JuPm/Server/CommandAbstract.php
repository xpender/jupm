<?php
abstract class JuPm_Server_CommandAbstract
{
    protected $_bJson;

    protected $_aParams;

    public function __construct($bJson, $aParams)
    {
        $this->_bJson = $bJson;

        $this->_aParams = $aParams;

        $this->render();
    }

    abstract public function render();
}
