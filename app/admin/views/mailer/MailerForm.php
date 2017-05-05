<?php
include "EmailSenderDictionary.php";

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

	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'EmailSender':
				return 'custom';
			default:
				return parent::getAttributeType( $attribute );
		}
	}

	function drawCustomAttribute( $attribute, $value, $tab_index, $view )
	{
		switch ( $attribute )
		{
			case 'EmailSender':
				$field = new EmailSenderDictionary();
				$field->SetId($attribute);
				$field->SetName($attribute);
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);

				echo $this->getName($attribute);
				$field->draw();
				break;

			default:
				parent::drawCustomAttribute( $attribute, $value, $tab_index, $view );
		}
	}
}