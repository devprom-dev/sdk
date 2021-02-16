<?php

class UpdateUploadForm extends AdminForm
{
 	function getAddCaption()
 	{
 		return text(1254);
 	}
 	
 	function getCommandClass()
 	{
 		return 'updateupload';
 	}

 	function getAttributes()
 	{
 		return array('Update');
 	}
 	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Update':
				return 'file';
		}
	}

	function getDescription( $attribute )
	{
		switch ( $attribute )
		{
			case 'Update':
				return text(1048);
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
		return translate('Загрузить');
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
 