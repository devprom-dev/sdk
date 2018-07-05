<?php

class CurlBuilder
{
    static public function getCurl()
    {
        $curl = curl_init();
        if ( EnvironmentSettings::getProxyServer() != '' ) {
            list($server, $port) = preg_split('/:/', EnvironmentSettings::getProxyServer());
            curl_setopt($curl, CURLOPT_PROXY, $server);
            if ( $port != '' ) {
                curl_setopt($curl, CURLOPT_PROXYPORT, $port);
            }
        }
        if ( EnvironmentSettings::getProxyAuth() != '' ) {
            curl_setopt($curl, CURLOPT_PROXYUSERPWD, EnvironmentSettings::getProxyAuth());
        }
        return $curl;
    }
}