<?php
class JuPm_Client_Command_Help extends JuPm_Client_CommandAbstract
{
    public function execute()
    {
        echo "jupm 0.1" . "\n";
        echo "\n";
        echo "Commands:\n";
        echo "  deploy - Deploys packages from jupm.conf into current project\n";
        echo "  clean - Removes packages in current project [!UNSAFE!]\n";
        echo "  buildpkg [--force] - Builds package based on package.json and src\n";
        echo "  list - List all packages in repositorys\n";
        echo "  list-upgrades - Checks repostiorys for new version of packages\n";
    }
}
