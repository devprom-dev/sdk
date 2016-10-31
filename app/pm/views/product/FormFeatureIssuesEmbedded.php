<?php

class FormFeatureIssuesEmbedded extends PMFormEmbedded
{
	const MAX_VISIBLE_TASKS = 5;
	
	private $terminal_states = array();
	private $hidden_tasks = 0;
	
 	protected function extendModel()
 	{
 		$this->terminal_states = $this->getObject()->getTerminalStates();

		$object = $this->getObject();
		foreach( array_keys($object->getAttributes()) as $attribute )
		{
			if ( $object->IsAttributeRequired($attribute) ) continue;
			$object->setAttributeVisible( $attribute, false );
		}
		$object->setAttributeVisible( 'Caption', true );
		$object->setAttributeVisible( 'OrderNum', false );
 	}
	
 	function getItemDisplayName( $object_it )
 	{
 		$uid = new ObjectUID;
		$text = $uid->getUidIcon( $object_it );
		$text .= ' '.$object_it->getWordsOnlyValue($object_it->getDisplayName(), 15);
		if ( $object_it->get('StateName') != '' ) $text .= ' ('.$object_it->get('StateName').')';
 		return $text;
 	}
 	
 	function getItemVisibility( $object_it )
 	{
 		if ( $object_it->getPos() >= self::MAX_VISIBLE_TASKS && in_array($object_it->get('State'), $this->terminal_states) ) {
 			$this->hidden_tasks++;
 			return false;
 		}
 		return parent::getItemVisibility( $object_it );
 	}

 	function createField( $attr )
 	{
 	    switch ( $attr )
 	    {
 	        default:
 	            return parent::createField( $attr );
 	    }
 	} 

	function getListItemsTitle() {
		if ( $this->hidden_tasks > 0 ) {
			return text(1014).' '.str_replace('%1', $this->hidden_tasks, text(1935));
		} else {
			return parent::getListItemsTitle();
		}
	}
}
