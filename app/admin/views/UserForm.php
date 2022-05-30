<?php
include_once SERVER_ROOT_PATH."core/classes/user/validators/ModelValidatorPasswordLength.php";
include_once SERVER_ROOT_PATH."core/classes/sprites/UserPicSpritesGenerator.php";
include "ui/FieldUserLicensesAttribute.php";

class UserForm extends AdminPageForm
{
	var $warning_message;

	function extendModel()
    {
        parent::extendModel();

        $object_it = $this->getObjectIt();

        $has_password = true;
        if ( is_object($object_it) && $object_it->getId() > 0 )
        {
            $factory_set = new AuthenticationFactorySet(getSession());
            foreach( $factory_set->getFactories() as $factory ) {
                if ( $factory->validateUser( $object_it ) ) {
                    $has_password = $has_password & $factory->credentialsRequired();
                }
            }
        }

        $object = $this->getObject();
        $object->addAttribute( 'RepeatPassword', 'PASSWORD', translate('Повтор пароля'), false, false, '', 61 );

        foreach( array('Password','RepeatPassword','AskChangePassword') as $attribute ) {
            $object->setAttributeRequired($attribute, $has_password);
            $object->setAttributeVisible($attribute, $this->getEditMode() && $has_password);
        }
        $object->setAttributeVisible( 'IsReadonly', true );

        foreach( $object->getAttributesByGroup('notifications-tab') as $attribute ) {
            $object->setAttributeVisible($attribute, true);
        }
    }

	function getValidators()
    {
        return array_merge(
            parent::getValidators(),
            array(
                new ModelValidatorPasswordLength()
            )
        );
    }

    function validateInputValues( $id, $action )
	{
		$result = parent::validateInputValues( $id, $action );
		if ( $result != '' ) return $result;

		if( $_REQUEST['Password'] != SHADOW_PASS && array_key_exists('RepeatPassword', $_REQUEST) ) {
			if($_REQUEST['Password'] != $_REQUEST['RepeatPassword']) {
				return text(235);
			}
		}
		
		if ( !$this->checkUniqueExcept($id, 'Login') ) {
			return text(214);
		}
		if ( $_REQUEST['Email'] != '' && !$this->checkUniqueExcept($id, 'Email') ) {
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

            case 'NotificationEmailType':
                $field = new FieldDictionary(getFactory()->getObject('Notification'));
                $field->setNullTitle(text(2451));
                return $field;

            case 'IsReadonly':
                return new FieldUserLicensesAttribute($this->getObjectIt());

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
	            if ( $value == '' ) {
	                return $this->object->getDefaultAttributeValue( $attr );
	            }
	            return $value;

            case 'AskChangePassowrd':
                $value = parent::getFieldValue( $attr );
                if ( $value == '' ) return 'Y';
                return $value;

	        default:
	            return parent::getFieldValue( $attr );
	    }
	}

	function getDefaultValue($field)
	{
		switch ( $field )
		{
            case 'NotificationEmailType':
                return '';
			default:
				return parent::getDefaultValue( $field );
		}
	}

	function getActions()
	{
		$actions = parent::getActions();
		
		$object_it = $this->getObjectIt();
		
		if ( !is_object($object_it) ) return $actions;

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

	function persist()
    {
        $wasUsers = $this->getObject()->getRecordCount();
        if ( parent::persist() ) {
            if ( $wasUsers < 1 ) {
                getSession()->setAuthenticationFactory(null);
                getSession()->open( $this->getObjectIt() );
            }
            return true;
        }
        return false;
    }

    function process()
    {
        $result = parent::process();

        if ( $result && $this->getAction() == 'add' ) {
            $generator = new UserPicSpritesGenerator();
            $generator->storeSprites();
            $this->invalidateCache(array('projects','apps','sessions'));
            $this->executeCheckpoints();
        }

        return $result;
    }

    function executeCheckpoints()
    {
        $checkpoint_factory = getCheckpointFactory();
        $checkpoint = $checkpoint_factory->getCheckpoint( 'CheckpointSystem' );
        $checkpoint->checkOnly( array('CheckpointHasAdmininstrator') );
    }

    function invalidateCache( array $paths ) {
        foreach( $paths as $path ) {
            getFactory()->getCacheService()->invalidate($path);
        }
    }
}
