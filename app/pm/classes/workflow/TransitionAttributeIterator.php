<?php

class TransitionAttributeIterator extends OrderedIterator
{
 	function getDisplayName() 
 	{
 		$user_name = translate(getFactory()->getObject($this->get('Entity'))->getAttributeUserName($this->get('ReferenceName')));
        if ( $user_name == '' ) {
            $user_name = preg_replace('/%1/', $this->get('ReferenceName'), text(1882));
        }

        $props = array();

        $props[] = in_array($this->get('IsVisible'), array('Y','on')) ? text(1802) : text(1801);
        $props[] = in_array($this->get('IsRequired'), array('Y','on')) ? text(1803) : '';
        $props[] = in_array($this->get('IsReadonly'), array('Y','on')) ? text(2239) : '';
        $props[] = in_array($this->get('IsMainTab'), array('Y','on')) ? text(2269) : '';

        $props = array_filter($props, function($value) {
            return $value != '';
        });

        if ( count($props) > 0 ) $user_name .= ' ('.join(',',$props).')';
 		return $user_name;
 	}
}
