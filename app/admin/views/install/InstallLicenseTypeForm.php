<?php

class InstallLicenseTypeForm extends AjaxForm
{
	function getAddCaption()
	{
		return text(1268);
	}

	function getCommandClass()
	{
		return 'chooselicensetype';
	}

	function getAttributes()
	{
		$attributes = getFactory()->getObject('License')->getRegistry()->getAll()->fieldToArray('LicenseType');
		
		$attributes[] = 'Redirect';
		
		return $attributes;
	}

	function getAttributeType( $attribute )
	{
		return 'custom';
	}

	function drawCustomAttribute( $attribute, $value, $tab_index )
	{
		global $tab_index;
		
		$license_it = getFactory()->getObject('License')->getRegistry()->getAll();
		
		$installed_it = getFactory()->getObject('LicenseInstalled')->getAll();
	
		while( !$license_it->end() )
		{
			if ( $license_it->get('LicenseType') == $attribute )
			{
				$title = $license_it->get('Caption');
				
				$description = $license_it->get('Description');
				
				$checked = $installed_it->get('LicenseType') == $license_it->get('LicenseType')
					|| $installed_it->get('LicenseType') == '' && $license_it->get('LicenseType') == 'LicenseTrial';
			}
			
			$license_it->moveNext();
		}
		
		echo '<label class="radio">';
		    echo '<input type="radio" name="LicenseType" value="'.$attribute.'" '.($checked ? 'checked' : '').' >';
		        echo '<h4 class="bs">'.$title.'</h4>';
		echo '</label>';
		
        echo '<span class="help-block">'.$description.'</span>';
		
		$tab_index++;						
	}
		
	function IsAttributeVisible( $attribute )
	{
		return $attribute != 'Redirect';
	}

	function getAttributeValue( $attribute )
	{
		global $_SERVER;
		
		switch ( $attribute )
		{
			case 'Redirect':
				$parts = preg_split('/\?/', $_SERVER['REQUEST_URI']);
				
				return _getServerUrl().$parts[0];
				
			default:
				return parent::getAttributeValue( $attribute );
		}
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
	
	function getTemplate()
	{
	    return 'admin/InstallLicenseTypeForm.php';
	}
	
    function getActions()
    {
		return array (
				array (
						'url' => "javascript: $('#action".$this->getId()."').val(1);",
						'name' => translate('Получить ключ'),
						'class' => 'btn-primary',
						'type' => 'submit'
				),
				array (
						'url' => "javascript: $('#action".$this->getId()."').val(3);",
						'name' => translate('Ввести ключ'),
						'type' => 'submit'
				)
		);    	
    }
	
	function getRenderParms()
	{
		$license_it = getFactory()->getObject('LicenseState')->getAll();

		if ( $license_it->get('LicenseKey') != '' && $license_it->get('IsValid') != 'Y' )
		{
		    $message = str_replace('%2', '/admin/license/', str_replace('%1', $license_it->restrictionMessage(), text(1428)));
		}
	    
	    return array_merge( parent::getRenderParms(), array (
	        'warning' => $message        
	    ));
	}
}
