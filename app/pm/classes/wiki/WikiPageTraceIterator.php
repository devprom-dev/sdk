<?php
use Devprom\ProjectBundle\Service\Widget\WidgetService;

class WikiPageTraceIterator extends OrderedIterator
{
    function getDisplayNameReference() {
        return 'TargetPage';
    }

 	function getDisplayName() {
		return WidgetService::getTraceDisplayName($this, $this->getRef($this->getDisplayNameReference()));
 	}

 	function getTraceDisplayName()
 	{
 		$object_it = $this->getRef( 'TargetPage' );
 		$uid = new ObjectUID($this->get($this->object->getBaselineReference()));
 		return translate('Трассировка').': '.$uid->getObjectUid($object_it).' '.$object_it->getDisplayName();
 	}
 	
 	function getBacktraceDisplayName()
 	{
 		$object_it = $this->getRef( 'SourcePage' );
 		$uid = new ObjectUID($this->get($this->object->getBaselineReference()));
 		return translate('Трассировка').': '.$uid->getObjectUid($object_it).' '.$object_it->getDisplayName();
 	}

 	function getKey( $source, $target ) {
 		return join( ',', array($source, $target) );
 	}
 	
 	function getRef( $attribute, $object = null )
 	{
 		switch ( $attribute )
 		{
 		    case 'SourcePage':
 		    case 'TargetPage':
 		    	$type_it = getFactory()->getObject('WikiType')->getExact($this->get($attribute.'ReferenceName'));
 		    	$class = getFactory()->getClass($type_it->get('ClassName'));
 		    	if ( !class_exists($class) ) return parent::getRef( $attribute );
 		    	return parent::getRef( $attribute, getFactory()->getObject($class) );
 		    	
 		    default:
 		    	return parent::getRef( $attribute );
 		}
 	}
}
