<?php

class EELDAPMetadataForm extends AdminForm
{
 	function getAddCaption()
 	{
 		return text(2799);
 	}
 	
 	function getCommandClass()
 	{
 		return 'ldapselectrules';
 	}

	function getAttributes()
	{
		return array( 'LoginAttribute', 'EmailAttribute', 'SearchAttributes' );
	}
	
	function getName( $attribute )
	{
		switch ( $attribute )
		{
			case 'LoginAttribute':
				return text(2800);
				
			case 'EmailAttribute':
				return text(2801);
				
			case 'SearchAttributes':
				return text(2769);
		}
	}

	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'SearchAttributes':
				return 'text';
				
			default:
				return 'text';
		}
	}

	function getAttributeValue( $attribute )
	{
		switch ( $attribute )
		{
			case 'LoginAttribute':
				return LDAP_LOGIN_ATTR;

			case 'EmailAttribute':
				return LDAP_EMAIL_ATTR;

			case 'SearchAttributes':
				return LDAP_ROOTQUERY;
		}
	}
	
	function getDescription( $attribute )
	{
		switch ( $attribute )
		{
			default:
				return '';
		}
	}
	
	function IsAttributeRequired( $attribute )
	{
		return true; 	
	}

	function IsAttributeVisible( $attribute )
	{
		return true;
	}

	function IsAttributeModifiable( $attribute )
	{
		return true;
	}

	function getButtonText()
	{
		return translate('Продолжить');
	}
}
