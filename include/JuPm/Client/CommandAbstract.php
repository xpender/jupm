<?php
abstract class JuPm_Client_CommandAbstract
{
    public function __construct()
    {
        $this->execute();
    }

    abstract public function execute();
}
