<?php

include ('methods/c_user_methods.php');

class BlackList extends PageList
{
	function BlackList( $object )
	{
		parent::PageList( $object );
	}

	function IsNeedToDisplayLinks( )
	{
		return false;
	}

	function IsNeedToModify( $object_it )
	{
		return false;
	}

	function IsNeedToDelete()
	{
		return false;
	}
	
	function getItemActions( $column_name, $object_it )
	{
		$actions = parent::getItemActions( $column_name, $object_it );

		if ( $object_it->get('SystemUser') > 0 )
		{
			$user_it = $object_it->getRef('SystemUser');
				
			$method = new UnBlockUserWebMethod;
			
			if ( $method->hasAccess() )
			{
				$actions[] = array( 
				    'name' => $method->getCaption(), 
				    'url' => $method->getJSCall(array('user' => $user_it->getId()))
				);
			}
		}

		return $actions;
	}

	function getColumns()
	{
		$this->object->addAttribute('Date', '', translate('Дата'), true);

		return parent::getColumns();
	}

	function drawCell( $object_it, $attr )
	{
		if( $attr == 'Date' )
		{
			echo $object_it->getDateTimeFormat('RecordCreated');
			return;
		}

		parent::drawCell( $object_it, $attr );
	}

	function getGroupDefault()
	{
		return '';
	}
}
