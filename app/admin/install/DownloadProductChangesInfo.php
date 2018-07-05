<?php

class DownloadProductChangesInfo extends Installable
{
	const PRODUCT_INFO_FILE = 'conf/rss.xml';
	const TIMEOUT = 10;

    function check() {
        return true;
    }

	function skip() {
		return !defined('UPDATES_URL');
	}
    
    function install()
    {
		$urlParts = parse_url(UPDATES_URL);
		$this->downloadRss($urlParts['scheme'].'://'.$urlParts['host'].'/rss?tag='.translate('Обновление').'&lang='.getLanguage()->getLanguage());

        return true;
    }

	protected function downloadRss( $url )
	{
		$curl = CurlBuilder::getCurl();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_HTTPGET, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, self::TIMEOUT);
		curl_setopt($curl, CURLOPT_REFERER, EnvironmentSettings::getServerUrl());

		$result = curl_exec($curl);
		if ( $result === false ) {
			$this->error( curl_error($curl) );
		}
		curl_close($curl);

		if ( $result == "" ) return;
		file_put_contents(DOCUMENT_ROOT.self::PRODUCT_INFO_FILE, $result);
	}
}
