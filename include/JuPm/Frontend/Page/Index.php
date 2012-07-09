<?php
class JuPm_Frontend_Page_Index extends JuPm_Frontend_PageAbstract
{
    public function display()
    {
        echo JuPm_Frontend_Template::head();

        echo '<div class="main">' . "\n";

        echo '<ul>';
        echo '<li><a href="/?action=list">Packages list</a></li>';
        echo '</ul>';

        echo '</div>' . "\n";

        echo JuPm_Frontend_Template::foot();
    }
}
