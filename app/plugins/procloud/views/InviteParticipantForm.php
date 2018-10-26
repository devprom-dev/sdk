<?php

class InviteParticipantForm extends PMForm
{
 	var $email;
 	
 	function InviteParticipantForm( $object, $email )
 	{
 		$this->email = $email;
 		
 		parent::__construct( $object );
 	}
 	
 	function getAddCaption()
 	{
 		return translate('Приглашение участника');
 	}

 	function getCommandClass()
 	{
 		return 'inviteuser&namespace=procloud';
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

	function getAttributeValue( $attribute )
	{
		return $this->email;
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
		return translate('Пригласить');
	}
}