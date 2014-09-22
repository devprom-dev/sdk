<?php

class BlogPostDatesRegistry extends ObjectRegistrySQL
{
	function getAll()
	{
	    $month_aggregate = new AggregateBase( 'RecordCreated' );
	        
	    $month_aggregate->setAlias('');
	    
	    $post = getFactory()->getObject('BlogPost');    
	    
		$post->addAggregate( $month_aggregate );
		
	    $month_aggregate->setGroupFunction('EXTRACT(YEAR_MONTH FROM ');
		
	    $post_it = $post->getAggregated('');

		$rowset = array();
		
		$monthes = getFactory()->getObject('DateMonth');
		
	    while( !$post_it->end() )
        {
        	$items = $post_it->get( $month_aggregate->getAggregateAlias() );
        	
            $names = array();
            
            preg_match('/([\d]{4})([\d]{2})/', $post_it->get('RecordCreated'), $names);
            
            $rowset[] = array (
            		'entityId' => $names[2].'-'.$names[1],
            		'Caption' => $monthes->getExact($names[2])->getDisplayName().' '.$names[1].' ('.$items.')'
            );

    		$post_it->moveNext();
        }                
		
		return $this->createIterator($rowset);
	}
}