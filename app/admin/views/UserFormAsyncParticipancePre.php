<?php

class UserParticipancePreForm extends AdminForm
{
	var $user_it;

	function UserParticipancePreForm ( $user_it )
	{
		global $model_factory;
		$this->user_it = $user_it;
			
		parent::AdminForm( $model_factory->getObject('pm_ParticipantRole') );
	}

	function getAddCaption()
	{
		return translate('Включение пользователя в проект');
	}

	function getCommandClass()
	{
		return 'prepareprojectparticipant';
	}

	function getAttributes()
	{
		$attrs = array('SystemUser');
		$attrs = array_merge($attrs, parent::getAttributes());

		return $attrs;
	}

	function getName( $attribute )
	{
		switch ( $attribute )
		{
			case 'SystemUser':
				return translate('Пользователь');

			default:
				return parent::getName( $attribute );
		}
	}

	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'SystemUser':
				return 'object';

			default:
				return parent::getAttributeType( $attribute );
		}
	}

	function getAttributeClass( $attribute )
	{
		global $model_factory;

		switch ( $attribute )
		{
			case 'SystemUser':
				return $model_factory->getObject('cms_User');

			case 'ProjectRole':
				return $model_factory->getObject('ProjectRoleBase');

			default:
				return parent::getAttributeClass( $attribute );
		}
	}

	function getAttributeValue( $attribute )
	{
		switch ( $attribute )
		{
			case 'SystemUser':
				return $this->user_it->getId();
		}

		return parent::getAttributeValue( $attribute );
	}

	function IsAttributeRequired( $attribute )
	{
		return true;
	}

	function IsAttributeVisible( $attribute )
	{
		switch ( $attribute )
		{
			case 'Capacity':
			case 'Participant':
			case 'ProjectRole':
				return false;
		}

		return true;
	}

	function IsAttributeModifable( $attribute )
	{
		switch ( $attribute )
		{
			case 'SystemUser':
				return false;
		}

		return true;
	}

	function getButtonText()
	{
		return translate('Продолжить');
	}
}
