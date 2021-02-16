<?php
use Devprom\ProjectBundle\Service\Widget\WidgetService;

class FunctionTraceIterator extends OrderedIterator
{
    function getDisplayNameReference() {
        return 'ObjectId';
    }
    
 	function getDisplayName()
 	{
 		$object_it = $this->getRef($this->getDisplayNameReference());

 		$uid = new ObjectUID;
 		if ( $uid->hasUid($object_it) ) {
 			$title = $uid->getUidWithCaption($object_it, 50);
 		}
 		else {
 			$title = $object_it->getDisplayName();
 		}

        if ( $this->get('IsActual') == 'N' ) {
            $title = WidgetService::getHtmlBrokenIcon(
                $object_it->getId(), getSession()->getApplicationUrl($object_it)) . $title;
        }

        return $title;
    }
 	
	function getObjectIt()
	{
		$object = getFactory()->getObject( $this->get('ObjectClass') );
		if( $this->get('ObjectId') == '' ) return $object->getEmptyIterator();
		return $object->getExact( $this->get('ObjectId') );
	}

 	function getTraceDisplayName()
 	{
 		$object_it = $this->getObjectIt();
 		$uid = new ObjectUID;
 		return translate('Трассировка').': '.$uid->getObjectUid($object_it).' '.$object_it->getDisplayName();
 	}
 	
 	function getBacktraceDisplayName()
 	{
 		$object_it = $this->getRef( 'Feature' );
 		$uid = new ObjectUID;
 		return translate('Трассировка').': '.$uid->getObjectUid($object_it).' '.$object_it->getDisplayName();
 	}
}