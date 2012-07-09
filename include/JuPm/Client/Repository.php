<?php
class JuPm_Client_Repository
{
    private $_sUrl;

    public function __construct($sUrl)
    {
        $this->_sUrl = $sUrl;
    }

    private function _exec($aRequest)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_sUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'json=' . json_encode($aRequest));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $curlerrno = curl_errno($ch);
        $curlerror = curl_error($ch);
        $httpstatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($curlerrno != CURLE_OK || $httpstatus != 200) {
            return array(
                'result' => 'ERROR',
                'error' => $curlerror,
                'http' => $httpstatus
                );
        }

        $decode = @json_decode($response, true);

        if (!is_array($decode)) {
            return array(
                'result' => 'ERROR',
                'error' => 'invalid json'
                );
        }

        return $decode;
    }

    public function ping()
    {
        $aResponse = $this->_exec(
            array(
                array(
                    'cmd' => 'ping'
                    )
                )
            );
    
        return array_shift($aResponse);
    }

    public function listpkgs() // I have PHP.. Why the hell list() is a reserved keyword?
    {
        $aResponse = $this->_exec(
            array(
                array(
                    'cmd' => 'list'
                    )
                )
            );

        return array_shift($aResponse);
    }

    public function query($sPackage, $sVersion)
    {
        $aResponse = $this->_exec(
            array(
                array(
                    'cmd' => 'query',
                    'name' => $sPackage,
                    'version' => $sVersion
                    )
                )
            );
    
        return array_shift($aResponse);
    }
}
