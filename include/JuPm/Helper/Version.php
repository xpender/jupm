<?php
class JuPm_Helper_Version
{
    public static function sort($sVersionA, $sVersionB)
    {
        return -1 * version_compare($sVersionA, $sVersionB);

        /** DOESN'T WORK
        
        $aVersionA = explode('.', $sVersionA);
        $aVersionB = explode('.', $sVersionB);

        // Normalize, we need to have same amount of numbers
        $cVersionA = count($aVersionA);
        $cVersionB = count($aVersionB);

        if ($cVersionA > $cVersionB) {
            $diff = $cVersionA - $cVersionB;

            for ($i = 0; $i < $diff; $i++) {
                $aVersionB[] = 0;
            }
        } elseif ($cVersionB > $cVersionA) {
            $diff = $cVersionB - $cVersionA;

            for ($i = 0; $i < $diff; $i++) {
                $aVersionA[] = 0;
            }
        }

        if ($aVersionA[0] > $aVersionB[0]) {
            return -1;
        } elseif ($aVersionA[0] == $aVersionB[0] && $aVersionA[1] > $aVersionB[1]) {
            var_dump($sVersionA);
            var_dump($sVersionB);
            echo '<hr />';
            return -1;
        } elseif ($aVersionA[0] == $aVersionB[0] && $aVersionA[2] == $aVersionB[2] && $aVersionA[3] > $aVersionB[3]) {
            return -1;
        } elseif ($aVersionA[0] == $aVersionB[0] && $aVersionA[2] == $aVersionB[2] && $aVersionA[3] < $aVersionB[3]) {
            return 1;
        } elseif ($aVersionA[0] == $aVersionB[0] && $aVersionA[1] < $aVersionB[1]) {
            return 1;
        } elseif ($aVersionA[0] < $aVersionB[0]) {
            return 1;
        }

        return 0;*/
    }
}
