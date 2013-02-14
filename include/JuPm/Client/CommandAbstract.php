<?php
abstract class JuPm_Client_CommandAbstract
{
    protected $_aCmdArguments;

    public function __construct($aCmdArguments = array())
    {
        $this->_aCmdArguments = $aCmdArguments;

        $this->execute();
    }

    abstract public function execute();
}
