<?php

class WorkTableStateRegistry extends ObjectRegistrySQL
{
	function getFilters()
	{
		$program_it = WorkTableProject::getProgramIt();
		
		$vpds = array_merge( array($program_it->get('VPD')), $program_it->getRef('LinkedProject')->fieldToArray('VPD') );
		
		return array (
				new FilterVpdPredicate( $vpds ),
				new FilterAttributePredicate( 'ObjectClass', 'request' )
		);
	}
}