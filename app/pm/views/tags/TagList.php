<?php

class TagList extends PageList
{
	function IsNeedToDisplay( $attr ) 
	{
		switch ( $attr )
		{
			default:
				return parent::IsNeedToDisplay( $attr );
		}
	}
	
	function IsNeedToDisplayLinks( ) 
	{ 
		return false; 
	}

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
                        if ( $widget_it->getId() != '' ) {
                            $url = $widget_it->getUrl('tag='.$object_it->getId());
                        }
                        else {
                            $url = $object->getPage().'tag='.$object_it->getId();
                        }
						echo '<a href="'.$url.'">';
							echo count(preg_split('/,/', $object_it->get($attr))); 
						echo '</a>';
					}
				}
		}
	}
	
	function drawCell( $object_it, $attr ) 
	{
		global $project_it, $model_factory;
		
		switch ( $attr )
		{
			case 'Caption':
				echo $object_it->getDisplayName();
				break;
				
			default:
				parent::drawCell( $object_it, $attr ); 
		}
		
		global $model_factory;
	}

	function getGroupFields()
	{
		return array();
	}
}
