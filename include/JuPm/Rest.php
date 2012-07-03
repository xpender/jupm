<?php
class JuPm_Rest
{
    public function __construct()
    {
        $this->_init();
    }

    private function _init()
    {
        
    }

    public function dispatch()
    {
        echo json_encode(array('error' => 'Wrong command..'));
    }
}
