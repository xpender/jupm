<?php
class JuPm_Frontend_Template
{
    public static function head()
    {
        $n = "\n"; $t = "\t";

        $sHtml  = '';
        $sHtml .= '<!doctype html>' . $n;
        $sHtml .= '<html>' . $n;
        $sHtml .= '<head>' . $n;
        $sHtml .= $t . '<title>jupm</title>' . $n;
        $sHtml .= $t . '<link rel="stylesheet" type="text/css" href="/jupm.css" />' . $n;
        $sHtml .= '</head>' . $n;
        $sHtml .= '<body>' . $n;
        $sHtml .= '<div class="title">jupm</div>';

        return $sHtml;
    }

    public static function back($sUrl)
    {
        $sHtml = '<div class="back"><a href="' . $sUrl . '">Back</a></div>' . "\n";

        return $sHtml;
    }

    public static function foot()
    {
        $n = "\n";

        $sHtml  = '';
        $sHtml .= '</body>' . $n;
        $sHtml .= '</html>' . $n;

        return $sHtml;
    }
}
