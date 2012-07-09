<?php
class JuPm_ServerApi_Controller
{
    private $_aCommands;

    public function __construct()
    {
        $this->_init();
    }

    private function _init()
    {
        $this->_registerCommand('list', 'JuPm_ServerApi_Command_List');
        $this->_registerCommand('query', 'JuPm_ServerApi_Command_Query');
        $this->_registerCommand('download', 'JuPm_ServerApi_Command_Download');
    }

    private function _registerCommand($sCommand, $sClassName)
    {
        $this->_aCommands[$sCommand] = $sClassName;
    }

    public function dispatch()
    {
        $sJson = $_REQUEST['json'];

        $aCommandBatch = json_decode($sJson, true);

        if (!is_array($aCommandBatch) || count($aCommandBatch) == 0) {
            echo json_encode(
                array(
                    'result' => 'ERROR',
                    'error' => 'invalid command batch'
                    )
                );
        
            exit;
        }

        $aResponse = array();

        foreach ($aCommandBatch as $iRequest => $aEntry) {
            if (!isset($this->_aCommands[$aEntry['cmd']])) {
                $aResponse[$iRequest] = array(
                    'result' => 'ERROR',
                    'error' => 'invalid command'
                    );

                continue;
            }

            $aResponse[$iRequest] = call_user_func(
                array($this->_aCommands[$aEntry['cmd']], 'execute'),
                $aEntry
                );
        }

        echo json_encode($aResponse);
    }
}
