<?php
class JuPm_Servercli_Command_Help extends JuPm_ServerCli_CommandAbstract
{
    public function execute()
    {
        echo "jupm 0.1" . "\n";
        echo "\n";
        echo "Commands:\n";
        echo "  builddb - Builds packages database\n";
    }
}
