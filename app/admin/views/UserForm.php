<?php

include (dirname(__FILE__).'/../methods/c_user_methods.php');

class UserForm extends AdminPageForm
{
	var $warning_message;

	function __construct( $object )
	{
		global $model_factory;

		$object = $model_factory->getObject('cms_User');

		parent::PageForm( $object );
		

		$object_it = $this->getObjectIt();

		$has_password = true;
		
		if ( is_object($object_it) && $object_it->getId() > 0 )
		{
		    $factory_set = new AuthenticationFactorySet(getSession());
		    
		    foreach( $factory_set->getFactories() as $factory )
		    {
    		    if ( $factory->validateUser( $object_it ) )
    		    {
    		        $has_password = $has_password & $factory->credentialsRequired();
    		    }
		    }
		}
		
		$object->addAttribute( 'RepeatPassword', 'PASSWORD', translate('Повтор пароля'), false, false, '', 65 );
		
		$object->setAttributeRequired('Password', $has_password);

	    $object->setAttributeRequired('RepeatPassword', $has_password);

	    $object->setAttributeVisible('Password', $this->getEditMode() && $has_password);

	    $object->setAttributeVisible('RepeatPassword', $this->getEditMode() && $has_password);
	}

	function validateInputValues( $id, $action )
	{
		global $_REQUEST, $model_factory;

		$result = parent::validateInputValues( $id, $action );
		
		if ( $result != '' ) return $result;

		if( $_REQUEST['Password'] != SHADOW_PASS && array_key_exists('RepeatPassword', $_REQUEST) )
		{
			if($_REQUEST['Password'] != $_REQUEST['RepeatPassword'])
			{
				return text(235);
			}
		}
		
		if(strpos($_REQUEST['Login'], '@') !== false)
		{
			return text(207);
		}

		if ( !$this->checkUniqueExcept($id, 'Login') )
		{
			return text(214);
		}

		if ( !$this->checkUniqueExcept($id, 'Email') )
		{
			return text(213);
		}
		
		if ( $id == '' ) return '';
		
		return '';
	}

	function IsNeedButtonNew() {
		return false;
	}
	function IsNeedButtonCopy() {
		return false;
	}

	function IsAttributeVisible( $attr_name )
	{
		switch ( $attr_name )
		{
			case 'OrderNum':
				return false;
					
			case 'IsAdmin':
			    return true;

			default:
				return parent::IsAttributeVisible( $attr_name );
		}
	}
	
	function createFieldObject( $name )
	{
		switch ( $name )
		{
			case 'Password':
			case 'RepeatPassword':
				return new FieldPassword;

			default:
				return parent::createFieldObject( $name );
		}
	}

	function getFieldValue( $attr )
	{
	    switch ( $attr )
	    {
	        case 'RepeatPassword':

	            return parent::getFieldValue( 'Password' );

	        case 'Language':
	            
	            $value = parent::getFieldValue( $attr );
	            
	            if ( $value == '' )
	            {
	                return $this->object->getDefaultAttributeValue( $attr );
	            }
	            
	            return $value;
	            
	        default:
	            return parent::getFieldValue( $attr );
	    }
	}
	
	function getActions()
	{
		$actions = parent::getActions();
		
		$object_it = $this->getObjectIt();
		
		if ( !is_object($object_it) ) return $actions;

		$method = new UserRelateToProjectWebMethod($object_it);
		if ( $method->hasAccess() )
		{
		    if ( $actions[count($actions) - 1]['name'] != '' ) array_push($actions, array( '' ) );
		    
		    $actions[] = array( 
		        'name' => $method->getCaption(),
				'url' => $method->getJSCall( array('user' => $object_it->getId()) ) 
		    );
		}

		if ( $object_it->get('Blocks') > 0 )
		{
			$method = new UnBlockUserWebMethod;
		}
		else
		{
			$method = new BlockUserWebMethod;
		}

		if ( $method->hasAccess() )
		{
		    if ( $actions[count($actions) - 1]['name'] != '' ) array_push($actions, array( '' ) );
		    
		    $actions[] = array( 
		        'name' => $method->getCaption(),
				'url' => $method->getJSCall( array('user' => $object_it->getId()) ) 
		    );
		}

		return $actions;
	}
}
