<?php
class JuPm_Server_Command_List extends JuPm_Server_CommandAbstract
{
    public function render()
    {
        $oPackagesDb = JuPm_Server_PackagesDb::getInstance();

        if ($this->_bJson) {
            echo json_encode(
                array(
                    'packages' => $oPackagesDb->allPackages()
                    )
                );
        } else {
            echo '<ul>';
            
            foreach ($oPackagesDb->allPackages() as $iPackageId => $aPackageName) {
                echo '<li><a href="/info/' . $iPackageId . '/">' . $aPackageName . '</li>';
            }

            echo '</ul>';
        }
    }
}
