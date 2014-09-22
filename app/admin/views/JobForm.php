<?php

class JobForm extends PageForm
{
	function __construct()
	{
		parent::__construct( getFactory()->getObject('co_ScheduledJob') );
	}

	function IsNeedButtonNew()
	{
		return false;
	}

	function IsNeedButtonCopy()
	{
		return false;
	}

	function createFieldObject( $name )
	{
		return parent::createFieldObject( $name );
	}

	function getFieldDescription( $name )
	{
		switch ( $name )
		{
			case 'Minutes':
			case 'Hours':
			case 'Days':
			case 'WeekDays':
				return text(680);
		}
	}
}