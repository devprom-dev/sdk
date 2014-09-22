<?php

class StateAttributeIterator extends OrderedIterator
{
 	function getDisplayName() 
 	{
 		$object = getFactory()->getObject($this->get('Entity'));
 		
 		$caption = translate($object->getAttributeUserName( $this->get('ReferenceName') ));
 		
 		if ( $caption == '' ) return '';
 		
 		$props = array();
 		
 		$props[] = in_array($this->get('IsVisible'), array('Y','on')) ? text(1802) : text(1801);
 		
 		$props[] = in_array($this->get('IsRequired'), array('Y','on')) ? text(1803) : '';
 		
 		$props = array_filter($props, function($value) {
 			return $value != '';
 		});
 				
 		if ( count($props) > 0 ) $caption .= ' ('.join(',',$props).')';
 		
 		return $caption;
 	}
}
