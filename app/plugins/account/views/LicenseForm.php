<?php

class LicenseForm extends AjaxForm
{
    function getCommandClass()
    {
        return 'getlicensekey';
    }
    
    function getFormUrl()
    {
    	return ACCOUNT_HOST.'/module/account/command?name='.$this->getCommandClass();
    }

    function getAttributes()
    {
    	$attributes = array();
    	
    	if ( getSession()->getUserIt()->getId() < 1 )
    	{
    		$user_it = getFactory()->getObject('User')->getRegistry()->Query(
					array (
							new FilterAttributePredicate('Email', $this->getAttributeValue('Email'))
					)
			);
    		
    		if ( $user_it->getId() != '' )
    		{
    			$attributes = array_merge($attributes, array('ExistPassword'));
    		}
    		else
    		{
	    		$attributes = array_merge($attributes, array('UserName', 'Email', 'UserPassword', 'UserForm'));
    		}
    	}
    	else
    	{
    		$attributes = array_merge($attributes, array('UserTitle', 'UserForm'));
    	}
    	
        if ( $_REQUEST['LicenseType'] == 'LicenseTeam' )
        {
            $attributes = array_merge($attributes, array('LicenseType', 'InstallationUID'));
        }
        else
        {
            $attributes = array_merge($attributes, array('LicenseType', 'InstallationUID', 'LicenseValue'));
        }
        
        if ( $this->processSaasProduct() )
        {
			$attributes[] = 'AggreementForm';
           	$attributes[] = 'Aggreement';
           	$attributes[] = 'PaymentServiceInfo';
        }
        
        return $attributes;
    }

    function getAttributeType( $attribute )
    {
        switch ( $attribute )
        {
            case 'InstallationUID':
            case 'LicenseValue':
            case 'UserName':
            case 'Email':
            	return 'text';

            case 'UserPassword':
            case 'ExistPassword':
            	return 'password';
            	
            case 'Aggreement':
                return 'char';

            case 'LicenseType':
            case 'PaymentServiceInfo':
            case 'UserForm':
            case 'UserTitle':
            case 'AggreementForm':
            	return 'custom';
        }
    }

    function IsAttributeVisible( $attribute )
    {
    	switch( $attribute )
    	{
    	    case 'InstallationUID':
    	    	return $this->getAttributeValue($attribute) == '';
    	}
    	
        return true;
    }

    function IsAttributeRequired( $attribute )
    {
        return false;
    }

    function IsAttributeModifable( $attribute )
    {
        return $attribute != 'InstallationUID';
    }
    
    function getName( $attribute )
    {
        switch ( $attribute )
        {
            case 'InstallationUID':
                return text('account2');
            case 'LicenseType':
                return text('account3');
            case 'LicenseValue':
           		return $this->processSaasProduct() ? text('account4') : text('account5');
            case 'Aggreement':
            	return text('account6');
            case 'PaymentServiceInfo':
            	return '';
            case 'UserName':
            	return text('account14');
            case 'Email':
            	return text('account15');
            case 'UserPassword':
            	return text('account16');
            case 'ExistPassword':
            	return text('account22');
            	
            default:
                return parent::getName( $attribute );
        }
    }

    function getAttributeValue( $attribute )
    {
        switch ( $attribute )
        {
            case 'InstallationUID':
            case 'LicenseType':
            case 'Email':
            case 'UserName':
            	if ( $_REQUEST[$attribute] != '' ) return $_REQUEST[$attribute];
        	    return parent::getAttributeValue( $attribute );

           case 'LicenseValue':
                if ( $_REQUEST[$attribute] != '' ) return $_REQUEST[$attribute];
                return $this->processSaasProduct() ? 12 : parent::getAttributeValue( $attribute ); 
            	
            case 'Aggreement': return 'N';
                
            default:
                return parent::getAttributeValue( $attribute );
        }
    }

    function getDescription( $attribute )
    {
        switch ( $attribute )
        {
            case 'ExistPassword':
            	return str_replace('%1', $this->getId(), text('account23'));
            	
            default:
                return '';
        }
    }

    function drawCustomAttribute( $attribute, $value, $tab_index )
    {
        switch( $attribute )
        {
        	case 'LicenseType':
				$field = new FieldDictionary(
					 $this->processSaasProduct() 
					 		? getFactory()->getObject('AccountProductSaas') 
					 		: getFactory()->getObject('AccountProduct')
            	);
				$field->SetName($attribute);
				$field->SetValue($value);
				$field->SetId($attribute);
				$field->SetTabIndex($tab_index);
				$field->setNullOption(false);
				
				echo $this->getName($attribute);
				$field->draw();
				
				echo '<input type="hidden" name="WasLicenseKey" value="'.htmlspecialchars($_REQUEST['WasLicenseKey']).'">';
				echo '<input type="hidden" name="WasLicenseValue" value="'.htmlspecialchars($_REQUEST['WasLicenseValue']).'">';
				echo '<input type="hidden" name="Redirect" value="'.htmlspecialchars($_REQUEST['Redirect']).'">';
				echo '<input type="hidden" name="Email" value="'.htmlspecialchars($_REQUEST['Email']).'">';
				break;
        		
        	case 'PaymentServiceInfo':
        		echo text('account13');
        		break;
        		
        	case 'UserTitle':
        		echo str_replace('%1', getSession()->getUserIt()->getDisplayName(), text('account17'));
        		break;
        		
        	case 'UserForm':
        		echo '<input type="hidden" name="Language" value="'.htmlspecialchars($_REQUEST['Language']).'">';
        		
        	case 'AggreementForm':
        		echo '<hr/>';
        		break;
        		
        	default:
        		return parent::drawCustomAttribute( $attribute, $value, $tab_index );
        }
    }

	function drawTitle()
	{
	}
	
	function getSubmitScript()
	{
		return '';
	}

	function getTemplate()
	{
		return '../../plugins/account/views/templates/account.tpl.php';
	}
	
	function getRenderParms()
	{
		$parms = parent::getRenderParms();
		
		$parms['buttons_template'] = '../../plugins/account/views/templates/buttons.tpl.php';
		
		return $parms;
	}

	protected function processSaasProduct()
	{
	    switch( $_REQUEST['LicenseType'] )
       	{
			case 'LicenseSAASALM':
			case 'LicenseSAASALMMiddle':
			case 'LicenseSAASALMLarge':
            	return true;
            	
			default:
				return false;
       	}
	}
}
