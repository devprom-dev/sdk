<?php

include_once "WikiPageTrace.php";

include "WikiPageInversedTraceIterator.php";

class WikiPageInversedTrace extends WikiPageTrace
{
 	function createIterator() 
 	{
 		return new WikiPageInversedTraceIterator( $this );
 	}

	function getBaselineReference()
	{
		return 'SourceBaseline';
	}
}
