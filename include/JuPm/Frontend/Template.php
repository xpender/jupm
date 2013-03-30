<?php
/**
 * jupm
 *
 * Very simple template engine using PHP based templates
 *
 * @package net.xpender.jupm
 * @author Marko Kercmar <m.kercmar@bigpoint.net>
 */
class JuPm_Frontend_Template
{
    /**
     * Path to templates folder
     *
     * @var string
     */
    protected $_sTemplatePath;

    /**
     * Assigned variables
     *
     * @var array
     */
    protected $_aVars = array();

    /**
     * __construct
     *
     * @param string $sTemplatePath
     */
    public function __construct($sTemplatePath)
    {
        $this->_sTemplatePath = $sTemplatePath;
    }

    /**
     * assign
     *
     * @param string $sName
     * @param string $mValue
     */
    public function assign($sName, $mValue)
    {
        $this->_aVars[$sName] = $mValue;
    }

    /**
     * get
     *
     * @param string $sName
     */
    public function get($sName)
    {
        if (isset($this->_aVars[$sName])) {
            return $this->_aVars[$sName];
        }

        return null;
    }

    /**
     * Displays template
     *
     * @param string $sTemplate
     */
    public function display($sTemplate)
    {
        $sFullPath = $this->_sTemplatePath . $sTemplate . '.php';

        if (!file_exists($sFullPath)) {
            throw new Exception(
                'Template ' . $sTemplate . ' does not exist (' . $sFullPath . ')'
                );
        }

        require $sFullPath;
    }
}
