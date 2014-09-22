<?php

class InstallForm extends AjaxForm
{
	function getAddCaption()
	{
		return text(990);
	}

	function getCommandClass()
	{
		return 'installsystem';
	}

	function getAttributes()
	{
		return array('MySQLHost', 'Database', 'SkipCreation', 'SkipStructure', 'DatabaseUser', 'DatabasePass');
	}

	function getName( $attribute )
	{
		switch ( $attribute )
		{
			case 'MySQLHost':
				return text(681);

			case 'Database':
				return text(682);

			case 'SkipCreation':
				return text(683);

			case 'SkipStructure':
				return text(931);

			case 'DatabaseUser':
				return text(684);

			case 'DatabasePass':
				return text(685);
		}
	}

	function getDescription( $attribute )
	{
		switch ( $attribute )
		{
			case 'SkipCreation':
				return text(686);

			case 'SkipStructure':
				return text(932);
		}
	}

	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'MySQLHost':
			case 'Database':
			case 'DatabaseUser':
			case 'DatabasePass':
				return 'text';

			case 'SkipCreation':
			case 'SkipStructure':
				return 'char';
		}
	}

	function getAttributeValue( $attribute )
	{
		if ( $attribute == 'MySQLHost' )
		{
			return 'localhost';
		}
		else if ( $attribute == 'DatabaseUser' )
		{
			return 'root';
		}
		else if ( $attribute == 'Database' )
		{
			return 'devprom';
		}
		else
		{
			return parent::getAttributeValue( $attribute );
		}
	}

	function IsAttributeRequired( $attribute )
	{
		return $attribute != 'SkipCreation' && $attribute != 'SkipStructure';
	}

	function IsAttributeVisible( $attribute )
	{
		return true;
	}

	function getButtonText()
	{
		return translate('Установить');
	}

	function getSite()
	{
		return 'admin';
	}

	function getWidth()
	{
		return '100%';
	}

	function IsCentered()
	{
		return false;
	}

	function getRedirectUrl()
	{
		return '/admin/install';
	}
}
