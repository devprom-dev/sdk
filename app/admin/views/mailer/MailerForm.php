<?php

class MailerForm extends AdminForm
{
    function getModifyCaption()
    {
        return text(1705);
    }

    function getFormUrl()
	{
		return '/admin/mailer/';
	}
	
	function getAttributeValue( $attribute )
	{
		switch ( $attribute )
		{
		    case 'AdminEmail':
		    	
		    	return getFactory()->getObject('cms_SystemSettings')->getAll()->getHtmlDecoded('AdminEmail');
		    	
		    default:
		    	
		    	return parent::getAttributeValue( $attribute );
		}
	}
}