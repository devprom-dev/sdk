<?php

class ImportXmlForm extends PMForm
{
 	function getAddCaption()
 	{
 		return text(373);
 	}
 	
 	function getCommandClass()
 	{
 		return 'requestsimportxml';
 	}

	function getAttributes()
	{
		return array('Excel', 'object'); 	
	}
	
	function getName( $attribute )
	{
		switch ( $attribute )
		{
			case 'Excel':
				return text(945); 	
		}
	}

	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Excel':
				return 'file'; 	

			case 'object':
				return 'custom'; 	
		}
	}

 	function getDescription( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'Excel':
 				return text(377);
 		}
 	}

	function IsAttributeRequired( $attribute )
	{
		switch ( $attribute )
		{
			case 'Excel':
				return true; 	
		}
	}

	function IsAttributeVisible( $attribute )
	{
		return true;
	}
	
	function getButtonText()
	{
		return translate('Импортировать');
	}

 	function getRedirectUrl()
	{
		return '';
		
		switch ( $this->getAction() )
		{
			case CO_ACTION_CREATE:
				return 'requests.php'; 
		}
	}
	
	function drawCustomAttribute( $attr )
	{
		global $_REQUEST;
		
		switch ( $attr )
		{
			case 'object':
				echo '<input type="hidden" name="object" value="'.htmlentities($_REQUEST['object']).'">';
				break;
				
			default:
				parent::drawCustomAttribute( $attr );
		}
	}
	
	function IsCentered()
	{
		return false;
	}
	
	function getWidth()
	{
		return '100%';
	}
	
	function IsPreviewEnabled()
	{
		return true;
	}
	
	function draw()
	{
		echo '<div style="padding-left:12px;padding-right:12px;">';
			parent::draw();
		echo '</div>';
	}
}