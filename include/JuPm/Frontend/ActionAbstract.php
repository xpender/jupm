<?php
/**
 * jupm
 *
 * @package net.xpender.jupm
 * @author Marko Kercmar <m.kercmar@bigpoint.net>
 */
abstract class JuPm_Frontend_ActionAbstract
{
    protected $_oTemplate;

    public function __construct()
    {
        // init template
        $this->_oTemplate = new JuPm_Frontend_Template(
            PROJECT_ROOT . '/include/JuPm/Frontend/Templates/'
            );

        // current action
        $this->_oTemplate->assign(
            'sCurrentAction',
            $this->getName()
            );

        // execute
        $this->_execute();
    }

    abstract public function getName();

    abstract protected function _execute();
}
