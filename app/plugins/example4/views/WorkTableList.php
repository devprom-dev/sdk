<?php

class WorkTableList extends PageList
{
	private $visible_columns = array(
			'UID', 'LinkedIssues', 'Priority', 'Caption', 'State', 'Customer', 'Department', 'Estimation', 'RecentComment', 'ReleaseFinishDate'
	);
	
	private $state_it = null;
	 
	// returns attribute name to group rows in the list
	function getGroup()
	{
		return 'Project';
	}
	
	function setupColumns()
	{
		parent::setupColumns();
		
		foreach( $this->getObject()->getAttributes() as $attribute => $data )
		{
			$this->getObject()->setAttributeVisible($attribute, in_array($attribute, $this->visible_columns));
		}
	}
	
	function drawCell( $object_it, $attribute )
	{
		switch ( $attribute )
		{
			case 'UID':
				
				$title = 'I-'.$object_it->getId();
				
				if ( $this->getStateIt($object_it)->get('IsTerminal') == 'Y' )
				{
					$title = '<strike>'.$title.'</strike>';
				}
				
				echo '<span style="white-space:nowrap;">'.$title.'</span>';
				
				break;
				
		    case 'State':
		    	
		    	echo $this->getStateIt($object_it)->getDisplayName();
		    	
		    	break;

		    case 'Description':
		    case 'Caption':
		    	
		    	echo $object_it->getHtmlDecoded($attribute);
		    	
		    	break;

		    case 'LinkedIssues':
		    	
		    	if ( $object_it->get($attribute) != '' )
		    	{
			    	$items = preg_split('/,/', $object_it->get($attribute));
	
			    	foreach( $items as $key => $item )
			    	{
			    		$items[$key] = '<span style="white-space:nowrap;">I-'.$item.'</span>';
			    	}
			    	
			    	echo join(', ', $items);
		    	}
		    	
		    	break;
		    	
		    case 'ReleaseFinishDate':
		    	
		    	if ( $object_it->get('State') == 'planned' )
		    	{
		    		parent::drawCell( $object_it, $attribute );
		    	}    		 
		    	
		    	break;
		    	
		    default:
		    	parent::drawCell( $object_it, $attribute );
		}
	}
	
	function getItemActions( $object_it )
	{
		return array();
	}
	
	function IsNeedToSelect()
	{
		return false;
	}
	
	private function getStateIt( $object_it )
	{
		if ( !is_object($this->state_it) )
		{
			$object = new WorkTableState();
			
			$this->state_it = $object->getAll();
		}
		
		$this->state_it->moveTo('ReferenceName', $object_it->get('State'));
		
		return $this->state_it->copy();
	}
}