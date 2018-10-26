<?php

class FindParticipantForm extends PMForm
{
 	function getAddCaption()
 	{
 		return translate('Поиск участника');
 	}

 	function getCommandClass()
 	{
 		return 'finduser&namespace=procloud';
 	}
 	
	function getAttributes()
	{
		return array( 'Email' );
	}
	
	function IsAttributeVisible( $attribute )
	{
		switch ( $attribute )
		{
			case 'Email':
				return true;
			
			default:
				return false;
		}
	}
 	
 	function getDescription( $attribute )
 	{
 		switch( $attribute )
 		{
 			case 'Email':
 				return text(449);
 		}
 	}

	function isCentered()
	{
		return false;
	}

	function getButtonText()
	{
		return translate('Найти');
	}
}