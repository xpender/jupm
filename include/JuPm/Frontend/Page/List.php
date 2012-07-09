<?php
class JuPm_Frontend_Page_List extends JuPm_Frontend_PageAbstract
{
    public function display()
    {
        echo JuPm_Frontend_Template::head();
        
        $oPackagesDb = JuPm_Server_PackagesDb::getInstance();

        echo '<div class="main">' . "\n";
           
        echo '<ul>';
            
        foreach ($oPackagesDb->allPackages() as $iPackageId => $sPackageName) {
            echo '<li><a href="/?action=info&package=' . $sPackageName . '">' . $sPackageName . '</li>';
        }

        echo '</ul>';

        echo '</div>' . "\n";

        echo JuPm_Frontend_Template::foot();
    }
}
