<?php

class TagList extends PMPageList
{
	function drawRefCell( $entity_it, $object_it, $attr )
	{
		switch( $attr )
		{
			default:
				if ( $object_it->get($attr) != '' )
				{
					$object = $object_it->object->getAttributeObject($attr);
					if ( is_object($object) )
					{
                        $widget_it = $this->getTable()->getReferencesListWidget($object, $attr);
                        $vpds = $entity_it->fieldToArray('VPD');

                        $url = $widget_it->getUrl(
                            'tag='.$object_it->getId(),
                            $this->getTable()->getReferencesListProjectIt($vpds)
                        );

                        echo count(array_unique(preg_split('/,/', $object_it->get($attr))));
						echo ' &nbsp; <a class="dashed" target="_blank" href="'.$url.'">' . text(2084) . '</a>';
					}
				}
		}
	}
	
	function drawCell( $object_it, $attr ) 
	{
		switch ( $attr )
		{
			case 'Caption':
				echo $object_it->getDisplayName();
				break;
			default:
				parent::drawCell( $object_it, $attr ); 
		}
	}

	function getGroupFields() {
		return array();
	}
}
