<?php
 
class PasswordForm extends AjaxForm
{
 	function getModifyCaption()
 	{
 		return translate('Изменение пароля');
 	}

    function getFormUrl()
    {
    }
    
 	function getRedirectUrl()
	{
		if ( $_REQUEST['redirect'] != '' ) return htmlentities($_REQUEST['redirect']);
		
		switch ( $this->getAction() )
		{
			case CO_ACTION_MODIFY:
				return '/profile';
		}
	}

	function getAttributes()
	{
		$attrs = array();
		
		$user_it = $this->getObjectIt();
		if ( $user_it->getId() < 1 )
		{
			array_push($attrs, 'CurrentPassword');
		}

		array_push($attrs, 'NewPassword');
		array_push($attrs, 'RepeatPassword');
		
		return $attrs;
	}

	function getAttributeType( $attribute )
	{
		return 'password';
	}
	
	function getName( $attribute )
	{
		switch ( $attribute )
		{
			case 'CurrentPassword':
				return translate('Текущий пароль');
				
			case 'NewPassword':
				return translate('Новый пароль');

			case 'RepeatPassword':
				return translate('Повтор нового пароля');
		}
	}

	function IsAttributeVisible( $attribute )
	{
		return true;
	}
 	
	function IsAttributeModifable( $attribute )
	{
		return true;
	}

	function IsAttributeRequired( $attribute )
	{
		return true;
	}
	
	function getWidth()
	{
		return '40%';
	}

	function getTemplate()
	{
		return "co/FormAsyncNoHeader.php";
	}
	
	function drawAttribute( $attribute )
	{
	    global $_REQUEST, $key;
	    
	    switch ( $attribute )
	    {
	        case 'NewPassword':
	     		
         		if ( $_REQUEST['key'] == '' )
         		{
         			$user_it = $this->getObjectIt();
         			$key = $user_it->getResetPasswordKey();
         		}
         		else
         		{
         			$key = htmlentities($_REQUEST['key']);
         		}
 			
	            echo '<input type="hidden" name="key" value="'.$key.'">';
	            
	        default:
	            
	            parent::drawAttribute( $attribute );
	    }
	}
}
