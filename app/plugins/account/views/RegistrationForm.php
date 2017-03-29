<?php

class RegistrationForm extends AjaxForm
{
    function getCommandClass() {
        return 'getlicensekey';
    }
    
    function getFormUrl() {
    	return ACCOUNT_HOST.'/module/account/command?name='.$this->getCommandClass();
    }

    function getAttributes()
    {
    	$attributes = array(
            'UserName', 'Email', 'Phone', 'Company'
        );
        return array_merge($attributes, array('LicenseType', 'InstallationUID'));
    }

    function getAttributeType( $attribute )
    {
        switch ( $attribute )
        {
            case 'InstallationUID':
            case 'UserName':
            case 'Phone':
            case 'Email':
            case 'Company':
            	return 'varchar';

            case 'LicenseType':
            	return 'custom';
        }
    }

    function IsAttributeVisible( $attribute ) {
        return !in_array($attribute, array('InstallationUID'));
    }

    function IsAttributeRequired( $attribute ) {
        return true;
    }

    function IsAttributeModifable( $attribute ) {
        return !in_array($attribute, array('InstallationUID'));
    }
    
    function getName( $attribute )
    {
        switch ( $attribute )
        {
            case 'UserName':
            	return text('account47');
            case 'Email':
            	return text('account48');
            case 'Phone':
            	return text('account49');
            case 'Company':
            	return text('account50');
        }
    }

    function getAttributeValue( $attribute )
    {
        switch ( $attribute )
        {
            case 'InstallationUID':
            case 'LicenseType':
            	if ( $_REQUEST[$attribute] != '' ) return $_REQUEST[$attribute];
        	    return parent::getAttributeValue( $attribute );
            default:
                return parent::getAttributeValue( $attribute );
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

    function drawCustomAttribute( $attribute, $value, $tab_index )
    {
        switch( $attribute )
        {
        	case 'LicenseType':
				echo '<input type="hidden" name="WasLicenseKey" value="'.htmlspecialchars($_REQUEST['WasLicenseKey']).'">';
				echo '<input type="hidden" name="WasLicenseValue" value="'.htmlspecialchars($_REQUEST['WasLicenseValue']).'">';
				echo '<input type="hidden" name="Redirect" value="'.htmlspecialchars($_REQUEST['Redirect']).'">';
                echo '<input type="hidden" name="LicenseScheme" value="'.htmlspecialchars($_REQUEST['LicenseScheme']).'">';
                echo '<input type="hidden" name="LicenseType" value="'.htmlspecialchars($_REQUEST['LicenseType']).'">';
                break;
        	default:
        		return parent::drawCustomAttribute( $attribute, $value, $tab_index );
        }
    }

	function drawTitle() {
	}
	
	function getSubmitScript() {
		return '';
	}

	function getTemplate() {
		return '../../plugins/account/views/templates/account.tpl.php';
	}
	
	function getRenderParms()
	{
		$parms = parent::getRenderParms();
		$parms['buttons_template'] = '../../plugins/account/views/templates/buttons.tpl.php';
		return $parms;
	}
}
