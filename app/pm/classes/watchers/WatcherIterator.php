<?php

class WatcherIterator extends OrderedIterator
{
 	function getDisplayName() 
 	{
        if ( $this->get('Email') != '' && $this->get('SystemUser') == '' ) {
            return $this->get('Email');
        }

        $user_it = $this->getRef('SystemUser');

        $title = $user_it->getDisplayName();
        if ( $user_it->get('Blocks') > 0 ) {
            $title = "<strike>{$title}</strike>";
        }

        return $title;
 	}

	function getAnchorIt()
	{
		$object = getFactory()->getObject($this->get('ObjectClass'));
		if ( !is_object($object) ) return $this->object->getEmptyIterator();
		
		return $object->getExact( $this->get('ObjectId') );
	}
}
