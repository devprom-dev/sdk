<?php

include_once "WikiPageDisplayRules.php";

class WikiPageTraceIterator extends OrderedIterator
{
    function getDisplayNameReference()
    {
        return 'TargetPage';
    }

 	function getDisplayName()
 	{
 		global $model_factory;
 		
 		$title = '';
 		
	 	if ( $this->getId() > 0 && $this->get('IsActual') == 'N' )
 		{
			$title .= '<img class="trace-state" id="trace-state-'.$this->getId().'" src="/images/exclamation.png" title="'.text(770).'"> ';
		}
		
		$title .= WikiPageDisplayRules::getTraceDisplayName($this, $this->getRef($this->getDisplayNameReference())); 

		return $title;
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

 	function getKey( $source, $target )
 	{
 		return join( ',', array($source, $target) );
 	}
 	
 	function getRef( $attribute, $object = null )
 	{
 		global $model_factory;
 		
 		switch ( $attribute )
 		{
 		    case 'SourcePage':
 		    case 'TargetPage':
 		    	
 		    	$type_it = getFactory()->getObject('WikiType')->getExact($this->get($attribute.'ReferenceName'));
 		    	
 		    	$class = $model_factory->getClass($type_it->get('ClassName'));

 		    	if ( !class_exists($class) ) return parent::getRef( $attribute ); 
 		    	
 		    	return parent::getRef( $attribute, $model_factory->getObject($class) );
 		    	
 		    default:
 		    	return parent::getRef( $attribute );
 		}
 	}
}
