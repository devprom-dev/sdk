<?php

class ScrumList extends PMPageList
{
 	var $participant;
 	
	function __construct( $object ) 
	{
		$object->setRegistry( new ScrumGrouppedRegistry() );
		
		parent::__construct($object);
		
		$this->participant = getFactory()->getObject('pm_Participant');
	}
	
	function IsNeedToDisplay( $attr ) 
	{
		switch ( $attr )
		{
			case 'Participant':
				return true;
				
			default:
				return parent::IsNeedToDisplay( $attr );
		}
	}
	
	function getColumnName( $attr_it ) 
	{
		return parent::getColumnName( $attr_it );
	}
	
	function getGroupFields() 
	{
		return array_merge( parent::getGroupFields(), array('GroupDate') );
	}
	
	function getGroupDefault() 
	{
		return 'GroupDate';
	}
	
	function drawGroup( $group_field, $object_it ) 
	{
		switch ( $group_field )
		{
			case 'GroupDate':
				echo $object_it->get('GroupDate');
				break;
				
			default:
				parent::drawGroup( $group_field, $object_it );
		}
	}
}