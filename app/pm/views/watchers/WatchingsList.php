<?php

class WatchingsList extends PMPageList
{
 	function getIterator() 
 	{
		return $this->object->getAllWatched( getSession()->getUserIt() );
	}
	
	function getColumns()
	{
		$this->object->addAttribute('Object', '', translate('Объект'), true);
		
		return parent::getColumns();
	}

	function IsNeedToDisplay( $attr ) 
	{
		return $attr == 'Object';
	}
	
	function IsNeedToDisplayLinks( ) { return false; }
	function IsNeedToDelete( ) { return false; }
	function IsNeedToModify( $object_it ) { return false; }

	function drawCell( $object_it, $attr ) 
	{
		global $model_factory;
		
		if( $attr == 'Object' ) 
		{
			$anchor_it = $object_it->getAnchorIt();
			
			if ( is_object($anchor_it) )
			{
			    $uid = new ObjectUID;
			    	
			    echo $anchor_it->object->getDisplayName().': ';
			    
			    $uid->drawUidInCaption( $anchor_it );
			}
		}
	}

	function getItemActions( $column_name, $object_it ) 
	{
		$actions = parent::getItemActions( $column_name, $object_it );

		$anchor_it = $object_it->getAnchorIt();
		
		if ( !is_object($anchor_it) ) return $actions;
		
		$method = new WatchWebMethod( $anchor_it );
		
		if ( $method->hasAccess() )
		{
			array_push($actions, array(
			    'name' => $method->getCaption(),
			    'url' => $method->getJSCall(
			        array('object' => $anchor_it->object->getClassName(), 'id' => $anchor_it->getId() )
			    )
			));
		}
		
		$class = strtolower(get_class($anchor_it->object));
		
		$session = getSession();
		
		array_push($actions, array( 
		    'url' => $session->getApplicationUrl().'project/log?object='.$anchor_it->object->getClassName().'&'.$class.'='.$anchor_it->getId(), 
		    'name' => translate('История изменений') 
		));

		return $actions;
	}
	
	function getGroupDefault()
	{
		return 'none';
	}
}