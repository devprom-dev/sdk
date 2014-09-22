<?php
 
class PasswordForm extends AjaxForm
{
 	function getModifyCaption()
 	{
 		return translate('��������� ������');
 	}

    function getFormUrl()
    {
    }
    
 	function getRedirectUrl()
	{
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
		
		array_push($attrs, 'page');
		
		return $attrs;
	}

	function getAttributeType( $attribute )
	{
		return 'password';
	}
	
	function getAttributeValue( $attribute )
	{
	    switch ( $attribute )
	    {
	        case 'page':
	            return $_REQUEST['page'];
	            
	        default:
	            return parent::getAttributeValue( $attribute );
	    }
	}
	
	function getName( $attribute )
	{
		switch ( $attribute )
		{
			case 'CurrentPassword':
				return translate('������� ������');
				
			case 'NewPassword':
				return translate('����� ������');

			case 'RepeatPassword':
				return translate('������ ������ ������');
		}
	}

	function IsAttributeVisible( $attribute )
	{
		return $attribute != 'page';
	}
 	
	function IsAttributeModifable( $attribute )
	{
		return true;
	}

	function IsAttributeRequired( $attribute )
	{
		return $attribute != 'page';
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
         			$key = $_REQUEST['key'];
         		}
 			
	            echo '<input type="hidden" name="key" value="'.$key.'">';
	            
	        default:
	            
	            parent::drawAttribute( $attribute );
	    }
	}
}
