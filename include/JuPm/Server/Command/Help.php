<?php
class JuPm_Server_Command_Help extends JuPm_Server_CommandAbstract
{
    private function _getUsageInfos()
    {
        $aUsageInfos = array();
        $aUsageInfos['list'] = 'list, List of all packages';
        $aUsageInfos['info'] = 'info [id], info about one package';
        $aUsageInfos['get'] = 'get [id], download of one package';

        return $aUsageInfos;
    }

    public function render()
    {
        $aUsageInfos = $this->_getUsageInfos();

        if ($this->_bJson) {
            echo json_encode(
                array(
                    'error' => 'Wrong command',
                    'usage' => $aUsageInfos
                    )
                );
        } else {
            echo '<ul>';
            echo '<li><a href="/list/">List of all packages</a></li>';
            echo '</ul>';
        }
    }
}
