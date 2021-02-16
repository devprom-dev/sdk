<?php

class EELDAPInfoForm extends AdminForm
{
 	function getAddCaption()
 	{
 		return text(2760);
 	}
 	
 	function getCommandClass()
 	{
 		return 'ldapcomplete';
 	}

	function getAttributes()
	{
		return array( 'Info', 'SubmitJob' );
	}
	
	function getName( $attribute )
	{
		switch ( $attribute )
		{
			case 'Info':
				return text(2780);

			case 'SubmitJob':
				return text(2781);
		}
	}

	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'SubmitJob':
				return 'char';
				
			default:
				return 'hugetext';
		}
	}

	function IsAttributeRequired( $attribute )
	{
		return false; 	
	}

	function IsAttributeVisible( $attribute )
	{
		return true;
	}

	function IsAttributeModifiable( $attribute )
	{
		return true;
	}

	function getAttributeValue( $attribute )
	{
		switch ( $attribute )
		{
			case 'Info':
				try
				{
					$logger = Logger::getLogger('LDAP');
					$appender = $logger->getAppender('LDAPFileAppender');
					
					if ( !is_object($appender) )
					{
					    return str_replace('%2', $logger->getName(), str_replace('%1', $logger->getConfigurationFile(), text(2803)));
					}
					
					if ( is_object($appender) )
					{
					    $file = fopen( $appender->getFileName(), 'r' );
					    $content = fread( $file, filesize($appender->getFileName()) );
					    fclose($file);
					}

					if ( trim($content) == '' )
					{
					    return str_replace('%2', $logger->getName(), str_replace('%1', $logger->getConfigurationFile(), text(2803)));
					}
					
					return join(PHP_EOL, array_reverse(explode(PHP_EOL, $content)));
				}
				catch( Exception $e)
				{
					error_log('Unable initialize logger: '.$e->getMessage());
					
					return "";
				}
				
			case 'SubmitJob':
				return 'Y';
				
			default:
				parent::getAttributeValue( $attribute );
		}
	}
	
	function getButtonText()
	{
		return translate('Завершить');
	}
}
