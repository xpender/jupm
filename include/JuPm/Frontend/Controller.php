<?php
/**
 * jupm
 *
 * @package net.xpender.jupm
 * @author Marko Kercmar <m.kercmar@bigpoint.net>
 */
class JuPm_Frontend_Controller
{
    public function dispatch($aRequest)
    {
        if (isset($aRequest['action'])) {
            $sAction = $aRequest['action'];
        } else {
            $sAction = 'index';
        }

        $sClassName = 'JuPm_Frontend_Action_' . ucfirst($sAction);
        new $sClassName;
    }
}
