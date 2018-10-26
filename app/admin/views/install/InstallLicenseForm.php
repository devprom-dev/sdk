<?php

class InstallLicenseForm extends AdminForm
{
 	function getModifyCaption()
 	{
 		return text(689);
 	}
 	
 	function getAddCaption()
 	{
 		return text(689);
 	}
 	
 	function getCommandClass()
 	{
 		return 'installlicense';
 	}

	function getAttributes()
	{
		$attributes = parent::getAttributes();
		
		$attributes[] = 'UserInfo';
		
		return $attributes;
	}
 	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
		    case 'UserInfo':
		    	return 'custom';
		    			
			default:
				return parent::getAttributeType( $attribute );
		}
	}

	function getName( $attribute )
	{
		switch ( $attribute )
		{
			default:
				return parent::getName( $attribute );
		}
	}
	
	function getDescription( $attribute )
	{
		switch ( $attribute )
		{
			default:
				return $this->object->getAttributeDescription($attribute);
		}
	}
	
	function getAttributeValue( $attribute )
	{
		switch ( $attribute )
		{
			case 'LicenseValue';
			case 'LicenseType';
			    return $_REQUEST[$attribute];

			case 'LicenseKey';
				return !array_key_exists($attribute, $_REQUEST) 
						? getFactory()->getObject('LicenseInstalled')->getAll()->get($attribute)
						: $_REQUEST[$attribute];
			    
			case 'InstallationUID': return INSTALLATION_UID;
				
			default:
				return parent::getAttributeValue( $attribute );
		}
	}
	
	function IsAttributeRequired( $attribute )
	{
		return in_array($attribute, array('LicenseKey', 'LicenseValue')); 	
	}

	function IsAttributeVisible( $attribute )
	{
	    $attrs = array('InstallationUID', 'LicenseKey', 'UserInfo');
	    
	    $object = $this->getObject();
	    
	    if ( $object->IsAttributeVisible( 'LicenseValue' ) )
	    {
	        $attrs[] = 'LicenseValue';
	    }
	    
		return in_array($attribute, $attrs); 	
	}
	
	function IsAttributeModifable( $attribute )
	{
		return !in_array($attribute, array('InstallationUID'));
	}
	
	function getButtonText()
	{
		return translate('Установить');
	}
	
	function drawCustomAttribute( $attribute, $value, $tab_index, $view )
	{
		switch ( $attribute )
		{
		    case 'UserInfo':
		    	$user_attributes = array('UName','UEmail','ULogin','UPassword');
		    	
		    	foreach( $user_attributes as $attribute )
		    	{
		    		echo '<input type="hidden" name="'.$attribute.'" value="'.IteratorBase::utf8towin(htmlentities(IteratorBase::wintoutf8($_REQUEST[$attribute]))).'">';
		    	}
		    	break;
		    	
		    default:
		    	parent::drawCustomAttribute( $attribute, $value, $tab_index, $view );
		}
	}

    function getRenderParms($view)
    {
        return array_merge(
            parent::getRenderParms($view),
            array(
                'actions_on_top' => false
            )
        );
    }
}
 
