<?php

include_once "MailerSettingsFile.php";

class MailerSettingsFileSendmail extends MailerSettingsFile
{
	public function read( $parameter )
	{
        $parms = parse_ini_file( $this->getIniPath() );

        $parameter = $this->mapParameterName($parameter);
        
		if ( $parameter == '' ) return '';
        
        return $parms[$parameter];
	}
	
	public function write( $parameter, $value )
	{
		$parameter = $this->mapParameterName($parameter);
		
		if ( $parameter == '' ) return;
		
		$content = file_get_contents($this->getIniPath());
		
		$content = preg_replace('/^'.$parameter.'=.*$/mi', $parameter.'='.$value, $content);
		
		file_put_contents($this->getIniPath(), $content);
	}
	
	public function exists()
	{
		return file_exists($this->getIniPath());
	}
	
	private function mapParameterName( $parameter )
	{
		switch ($parameter)
		{
		    case 'MailServer':
		    	return 'smtp_server';
		    	
		    case 'MailServerPort':
		    	return 'smtp_port';

		    case 'MailServerEncryption':
		    	return 'smtp_ssl';
		    	
		    case 'MailServerUser':
		    	return 'auth_username';

		    case 'MailServerPassword':
		    	return 'auth_password';
		}
		
		return '';
	}
	
	private function getIniPath()
	{
		return defined('SERVER_SENDMAIL_PATH') ? SERVER_SENDMAIL_PATH : SERVER_ROOT.'/smtp/sendmail.ini';
	}
}