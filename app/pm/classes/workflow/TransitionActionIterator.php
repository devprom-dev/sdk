<?php

class TransitionActionIterator extends OrderedIterator
{
    function getActionOnlyName() {
        return $this->getRef('ReferenceName', getFactory()->getObject('StateBusinessAction'))->getDisplayName();
    }

 	function getDisplayName() 
 	{
 		$name = $this->getActionOnlyName();

        $details = array();
        if ( $this->get('Parameters') != '' ) {
            $details[] = $this->get('Parameters');
        }
        if ( $this->get('IsNotifyUser') == 'Y' ) {
            $details[] = text(3311);
        }
        if ( count($details) > 0 ) {
            $name .= " (" . join(', ', $details) . ")";
        }

 		return $name != '' ? $name : text(2008);
 	}
}
