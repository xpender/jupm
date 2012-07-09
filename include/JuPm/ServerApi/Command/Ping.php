<?php
class JuPm_ServerApi_Command_Ping implements JuPm_ServerApi_CommandInterface
{
    public static function execute($aRequest)
    {
        return array(
            'result' => 'OK'
            );
    }
}
