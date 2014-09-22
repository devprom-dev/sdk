<?php

class TagIterator extends OrderedIterator
{
 	function canDetach()
 	{
 		global $part_it;
 		
 		if ( $this->get('Owner') < 1 ) return true;
 		return $this->get('Owner') == $part_it->getId();
 	}
 	
 	function getDisplayName()
 	{
 		global $model_factory;
 		
 		$display_name = parent::getDisplayName();
 		
 		if ( $this->get('Owner') > 0 ) 
		{
			$part = $model_factory->getObject('pm_Participant');
			$participant_it = $part->getExact($this->get('Owner'));
			
			if ( $participant_it->count() > 0 )
			{
				$display_name .= '©'.$participant_it->getDisplayName().'';
			}
		}
 		
 		return $display_name;
 	}

	function getViewUrl() 
	{
		return $this->object->getPageNameViewMode($this->get('TagId'));
	}
}