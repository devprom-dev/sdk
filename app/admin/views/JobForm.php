<?php

class JobForm extends PageForm
{
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