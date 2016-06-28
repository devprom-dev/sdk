<?php

use Symfony\Component\Yaml\Yaml;

include_once "MailerSettingsFile.php";

class MailerSettingsFileSwiftMailer extends MailerSettingsFile
{
	public function read( $parameter )
	{
        $settings = Yaml::parse(file_get_contents($this->getSettingsPath()));
        return $settings['parameters'][$this->mapParameterName($parameter)];
	}
	
	public function write( $parameter, $value )
	{
        $settings = Yaml::parse(file_get_contents($this->getSettingsPath()));
		
        if ( $parameter == 'MailServerEncryption' && !in_array($value, array('tls','ssl')) ) $value = null; 
        
        $settings['parameters'][$this->mapParameterName($parameter)] = $value;
        
		file_put_contents($this->getSettingsPath(), Yaml::dump($settings));
	}
	
	public function exists() {
		return file_exists($this->getSettingsPath());
	}
	
	private function mapParameterName( $parameter )
	{
		switch ($parameter)
		{
		    case 'MailServer':
		    	return 'mailer_host';
		    	
		    case 'MailServerPort':
		    	return 'mailer_port';

			case 'Pop3Server':
				return 'pop3_host';

			case 'Pop3ServerPort':
				return 'pop3_port';

		    case 'MailServerEncryption':
		    	return 'mailer_encryption';
		    	
		    case 'MailServerUser':
		    	return 'mailer_user';

		    case 'MailServerPassword':
		    	return 'mailer_password';
		    	
		    case 'MailServerType':
		    	return 'mailer_transport';
		}
	}
	
	private function getSettingsPath() {
		return SERVER_ROOT_PATH.'co/bundles/Devprom/ApplicationBundle/Resources/config/settings.yml';
	}
}