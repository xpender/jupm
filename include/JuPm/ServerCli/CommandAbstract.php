<?php
abstract class JuPm_ServerCli_CommandAbstract
{
    public function __construct()
    {
        $this->execute();
    }

    abstract public function execute();
}
