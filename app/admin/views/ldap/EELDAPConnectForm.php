<?php

class EELDAPConnectForm extends AdminForm
{
 	function getAddCaption()
 	{
 		return text(2760);
 	}
 	
 	function getCommandClass()
 	{
 		return 'ldapmetadata';
 	}

	function getAttributes()
	{
		return array( 'LDAPServer', 'DirectoryType', 'UserName', 'Password', 'SearchDomain' );
	}
	
	function getName( $attribute )
	{
		switch ( $attribute )
		{
			case 'LDAPServer':
				return text(2761);
				
			case 'DirectoryType':
				return text(2802);
				
			case 'UserName':
				return text(2762);
				
			case 'Password':
				return text(2763);
				
			case 'SearchDomain':
				return text(2770);
		}
	}

	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Password':
				return 'password';
				
			case 'DirectoryType':
				return 'custom';
				
			default:
				return 'text';
		}
	}

	function getAttributeValue( $attribute )
	{
		switch ( $attribute )
		{
			case 'LDAPServer':
				return LDAP_SERVER;

			case 'DirectoryType':
				return LDAP_TYPE;
				
			case 'UserName':
				return LDAP_USERNAME;

			case 'Password':
				return LDAP_PASSWORD;

			case 'SearchDomain':
				return LDAP_DOMAIN;
		}
	}
	
	function getDescription( $attribute )
	{
		switch ( $attribute )
		{
			case 'LDAPServer':
				return text(2771);
				
			case 'UserName':
				return text(2797);

			default:
				return '';
		}
	}
	
	function IsAttributeRequired( $attribute )
	{
		return $attribute != 'SearchDomain'; 	
	}

	function IsAttributeVisible( $attribute )
	{
		return true;
	}

	function IsAttributeModifiable( $attribute )
	{
		return true;
	}

	function drawCustomAttribute( $attribute, $value, $tab_index, $view )
	{
		global $tab_index;
		
		switch( $attribute )
		{
			case 'DirectoryType':
				$licenses = array (
					'ad' => 'Active Directory',
					'apacheds' => 'Apache DS',
					'openldap' => 'OpenLDAP'
				);
				
				$type = $this->getAttributeValue( $attribute );
				
				echo '<select id="'.$attribute.'" name="'.$attribute.'" value="'.$value.'" style="width:100%;">';
					foreach( $licenses as $license => $title )
					{
						echo '<option value="'.$license.'" '.($license == $type ? 'selected' : '').'>'.$title.'</option>';
					}
				echo '</select>';
				
				return;
		}
		
		return parent::drawCustomAttribute( $attribute, $value, $tab_index, $view );
	}
	
	function getButtonText()
	{
		return translate('Продолжить');
	}
}
