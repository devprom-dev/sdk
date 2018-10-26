<?php

class RegisterToCourseForm extends AjaxForm
{
    function __construct( $object )
    {
        foreach( $object->getAttributes() as $key => $data ) {
            $object->setAttributeVisible($key, false);
        }

        foreach( array('Caption', 'Email') as $attribute ) {
            $object->setAttributeVisible($attribute, true);
        }

        parent::__construct($object);
    }

    function getModifyCaption()
    {
        return translate('Изменение пароля');
    }

    function getCommandClass()
    {
        return 'registercourse&namespace=coaching';
    }

    function getWidth()
	{
		return '40%';
	}

    function getButtonText()
    {
        return 'Зарегистрироваться';
    }

    function getTemplate()
    {
        return "co/FormAsyncNoHeader.php";
    }
}