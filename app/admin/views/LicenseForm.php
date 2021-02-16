<?php

class LicenseForm extends AdminForm
{
    function getModifyCaption()
    {
        return text(1278);
    }

    function getCommandClass()
    {
        return 'installlicense';
    }

    function getName( $attribute )
    {
        switch ( $attribute )
        {
            case 'Caption':
                return text(1276);

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
            case 'Caption':
                return $this->object->getAttributeUserName($attribute);

            default:
                return parent::getAttributeValue( $attribute );
        }
    }

    function IsAttributeVisible( $attribute )
    {
        if ( $attribute == 'LicenseType' ) return false;
        
        return parent::IsAttributeVisible( $attribute );
    }

    function IsAttributeModifiable( $attribute )
    {
        return false;
    }

    function getActions()
    {
		return array (
				array (
						'url' => "javascript: window.location = '?LicenseType=';",
						'name' => translate('Изменить'),
						'class' => 'btn-primary'
				)
		);    	
    }
    
    function getTemplate()
    {
        return "admin/LicenseForm.php";
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