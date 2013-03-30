<?php
class JuPm_Frontend_Action_Index extends JuPm_Frontend_ActionAbstract
{
    public function getName()
    {
        return 'index';
    }

    protected function _execute()
    {
        $this->_oTemplate->display(
            'index'
            );
    }
}
