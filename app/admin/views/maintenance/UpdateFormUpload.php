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
	
	function IsAttributeModifable( $attribute )
	{
		return true;
	}
	
	function getButtonText()
	{
		return translate('Загрузить');
	}
}
 