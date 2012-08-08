<?php
class JuPm_Frontend_Controller
{
    public function __construct()
    {
    }

    public function dispatch()
    {
        if (isset($_REQUEST['action'])) {
            $sAction = $_REQUEST['action'];
        } else {
            $sAction = 'index';
        }

        $aValidActions = array(
            'list',
            'info',
            'index'
            );

        if (!in_array($sAction, $aValidActions)) {
            $sAction = 'index';
        }

        $sClassName = 'JuPm_Frontend_Page_' . ucfirst($sAction);

        new $sClassName();
    }
}
