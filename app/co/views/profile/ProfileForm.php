<?php
 
class ProfileForm extends AjaxForm
{
	function getModifyCaption()
 	{
 	    $object_it = $this->getObjectIt();

		$user_pic_html = $this->getView()->render('core/UserPicture.php', array (
				'id' => $object_it->getId(), 
				'class' => 'user-pic',
				'title' => $object_it->getDisplayName()
		));
 	    
 	    return $user_pic_html.' '.text(268);
 	}

 	function getCommandClass()
 	{
 		return 'profilemanage';
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

	function IsAttributeVisible( $attribute )
	{
		switch ( $attribute )
		{
			case 'Caption':
			case 'Email':
			case 'Language':
			case 'Phone':
			case 'Photo':
                return true;

			case 'Login':
				return $this->IsAttributeModifiable($attribute);
			
			default:
			    if ( in_array($attribute, $this->getObject()->getAttributesByGroup('notifications-tab')) ) return false;
				return false;
		}
	}
 	
	function IsAttributeModifiable( $attribute )
	{
		switch ( $attribute )
		{
			case 'Password':
			case 'IsAdmin':
			case 'IsShared':
				return false;
			
			case 'Login':
				return getFactory()->getObject('cms_SystemSettings')->getAll()->get('AllowToChangeLogin') == 'Y';

			default:
				return true;
		}
	}

	function getAttributeType( $attribute )
	{
	    switch ( $attribute )
	    {
	        case 'Photo':
	            return 'file';

	        default:
	            return parent::getAttributeType( $attribute );
	    }
	}
	
 	function getDescription( $attribute )
 	{
 		switch( $attribute )
 		{
 			case 'Caption':
 				return text(269);
 			case 'Email':
 				return text(270);
 			case 'Login':
 				return text(271);
 			case 'Language':
 				return text(272);
 			case 'Skills':
 				return text(273);
 			case 'Tools':
 				return text(274);
            case 'NotificationEmailType':
                return text(2469);
            case 'NotificationTrackingType':
                return text(2468);
            case 'Phone':
 				return ' ';
 		}
 	}
 	
 	function getActions()
 	{
 	    $session = getSession();
 	    
 	    $user_it = $session->getUserIt();
 	    
 	    $actions = parent::getActions();
 	    
 	    $session = getSession();
 	    
 	    $auth_factory = $session->getAuthenticationFactory();
 	    	
 	    if ( $auth_factory->credentialsRequired() )
 	    {
 	        $actions[] = array (
 	            'name' => translate('Изменить пароль'),
 	            'url' => '/reset?key='.$user_it->getResetPasswordKey()
 	        );
 	    }
 	    
 	    return $actions;
 	}
}
