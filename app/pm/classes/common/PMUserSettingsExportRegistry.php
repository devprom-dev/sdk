<?php

class PMUserSettingsExportRegistry extends ObjectRegistrySQL
{
	function getFilters()
	{
		return array_merge(
				parent::getFilters(),
				array (
					new FilterAttributePredicate('Participant', 
							array(
	 	    						getSession()->getParticipantIt()->getId(), // search for user's settings 
	 	    						'-1' // search for common settings
	 	    				)
	 	    		),
					new FilterBaseVpdPredicate()
				)
		);
	}
	
	function getSorts()
	{
		return array_merge(
				parent::getSorts(),
				array (
						new SortAttributeClause('Participant.D')
				)
		);
	}
}
