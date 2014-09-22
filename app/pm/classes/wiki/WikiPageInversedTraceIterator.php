<?php

class WikiPageInversedTraceIterator extends WikiPageTraceIterator
{
	function getDisplayName()
	{
		$title = parent::getDisplayName();
		
		if ( $this->get('Baseline') > 0 )
	 	{
	 		$baseline_it = $this->getRef('Baseline');
	 		
	 		$title .= " [".$baseline_it->getDisplayName()."]";
	 	}
	 	
		return $title;
	}
	
    function getDisplayNameReference()
    {
        return 'SourcePage';
    }
}
