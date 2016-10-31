<?php

class SnapshotList extends PMPageList
{
	private $objects_cache = array();
	
	function retrieve()
	{
		parent::retrieve();
		
		$it = $this->getIteratorRef();
		
		$classes = array_filter($it->fieldToArray('ObjectClass'), function($value) {
			return $value != '';
		});
		
		foreach( $classes as $class_name )
		{
			$object_class = getFactory()->getClass($class_name);
			
			if ( !class_exists($object_class) ) continue;
			
			$ids = array_filter($it->fieldToArray('ObjectId'), function($value) {
				return $value > 0;
			});
			
			if ( count($ids) < 1 ) $ids[] = 0;
			
			$this->objects_cache[$object_class] = getFactory()->getObject($object_class)->getRegistry()->Query(
					array(
							new FilterInPredicate($ids)
					)
			);
		}
	}
	
	function IsNeedToDisplay( $attr )
    {
         switch( $attr )
         {
             case 'RecordCreated':
                 return true;

             case 'ListName':
                 return false;
                 
             default:
                 return parent::IsNeedToDisplay( $attr );
         }
    }
    
    function drawCell( $object_it, $attr )
    {
    	switch ( $attr )
    	{
    	    case 'Caption':
    	    	
    	    	$it = $this->objects_cache[$object_it->get('ObjectClass')];
    	    	
    	    	if ( $it instanceof IteratorBase )
    	    	{
    	    		$it->moveToId($object_it->get('ObjectId'));
    	    		
					$uid = new ObjectUID;
					
					if ( $uid->hasUid($it) )
					{
						$info = $uid->getUidInfo($it, true);
						$url = $info['url'];
						$title = '['.$info['uid'].'] '.$info['caption']; 
					}
					else
					{
						$url = $it->getViewUrl();
						$title = $it->getDisplayName();
					}

					if ( $object_it->get('Type') == 'branch' ) {
						$title = translate('Бейзлайн').': '.$title;
					}
					else {
						$title = translate('Версия').': '.$title;
						$url .= (strpos($url, '?') !== false ? '&' : '?').'baseline='.$object_it->getId();
					}
					
					echo '<a href="'.$url.'">'.$title.'</a>';
    	    	}

				// version of the branch included in the object's title already
				if ( $object_it->get('Type') != 'branch' )
				{
					echo ': ';
					
					parent::drawCell( $object_it, $attr );
				}
    	    	
    	    	break;
    	    	
    	    default:
    	    	
    	    	parent::drawCell( $object_it, $attr );
    	}
    }
}
 