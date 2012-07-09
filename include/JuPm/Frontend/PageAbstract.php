<?php
abstract class JuPm_Frontend_PageAbstract
{
    public function __construct()
    {
        $this->display();
    }

    abstract public function display();
}
