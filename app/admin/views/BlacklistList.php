<?php

class BlacklistList extends PageList
{
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
				if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
				$actions[] = array( 
				    'name' => $method->getCaption(), 
				    'url' => $method->getJSCall(array('user' => $user_it->getId()))
				);
			}
		}

		return $actions;
	}

	function extendModel()
    {
        parent::extendModel();
        $this->getObject()->addAttribute('Date', '', translate('Дата'), true);
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
