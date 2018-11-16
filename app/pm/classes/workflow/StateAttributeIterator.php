<?php

class StateAttributeIterator extends OrderedIterator
{
 	function getDisplayName() 
 	{
 	    $className = getFactory()->getClass($this->get('Entity'));
 	    if ( !class_exists($className) ) return "";

 	    $object = getFactory()->getObject($className);
 	    if ( !is_object($object) ) return "";

 		$caption = translate($object->getAttributeUserName( $this->get('ReferenceName') ));
 		if ( $caption == '' ) return '';
 		
 		$props = array();
 		
 		$props[] = in_array($this->get('IsVisible'), array('Y','on')) ? text(1802) : text(1801);
 		$props[] = in_array($this->get('IsRequired'), array('Y','on')) ? text(1803) : '';
        $props[] = in_array($this->get('IsReadonly'), array('Y','on')) ? text(2239) : '';
        $props[] = in_array($this->get('IsMainTab'), array('Y','on')) ? text(2269) : '';
        $props[] = in_array($this->get('IsAskForValue'), array('Y','on')) ? text(2541) : '';

 		$props = array_filter($props, function($value) {
 			return $value != '';
 		});
 				
 		if ( count($props) > 0 ) $caption .= ' ('.join(',',$props).')';
 		return $caption;
 	}
}
