<?php

class FunctionTraceList extends FunctionList
{
 	var $trace_map, $all_agg_it;
 	
 	function __construct( $object )
 	{
 		parent::__construct( $object );
 	}
 	
	function retrieve()
	{
		global $model_factory;

		parent::retrieve();
		
		// cache requests per feature
		$filters = $this->getFilterValues();
		
		$request = $model_factory->getObject('pm_ChangeRequest');
		
		$request->addFilter( new RequestStagePredicate($filters['stage']) );
		
		$this->all_agg_it = $request->getRequestsAggByFunction();
		
		$this->all_agg_it->buildPositionHash( array('Function') );
	}
 	
 	function setupColumns()
 	{
		$this->object->addAttribute('Issues', '', translate('Пожелания'), true, false, '', 40);
 	    
		parent::setupColumns();
 	    
 	    $visible = array_merge( 
 	    		array(
 	    				'UID', 
 	    				'Caption', 
 	    				'Issues'
 	    		), 
		    	$this->getObject()->getAttributesByGroup('trace')
		);

		$attrs = $this->object->getAttributes();
		
		foreach( $attrs as $key => $attr )
		{
			$this->object->setAttributeVisible( $key, in_array($key, $visible) ); 
		}
 	}
	
	function drawCell( $object_it, $attr )
	{
		global $model_factory;
		
	    if( $attr == 'Issues' ) 
		{
			$this->all_agg_it->setStop( 'Function', $object_it->getId() );

			$session = getSession();
			
			$values = $this->getFilterValues();
			
			$report_it = $model_factory->getObject('PMReport')->getExact('productbacklog');
			
			if ( getFactory()->getAccessPolicy()->can_read($report_it) )
			{
			    $item = $report_it->buildMenuItem('&state=all&function='.$object_it->getId());

			    $url = $item['url'];
			
    			if ( $values['view'] != '' )
    	 		{
    	 			$url .= '&view='.$values['view'];
    	 		} 		
    
    			if ( $values['stage'] != '' )
    	 		{
    	 			$version = $model_factory->getObject('Stage');
    	 			$version_it = $version->getExact( $values['stage'] );
    	 			
    	 			if ( $version_it->getId() != '' )
    	 			{
    	 				$url .= '&release='.$version_it->get('Version');
    	 			}
    	 		} 		
    
    			$frame = new IssuesGroupFrame( $this->all_agg_it, false );
    			$frame->setUrl( $url );
    			$frame->draw();
			}
		}
		else 
		{
			parent::drawCell( $object_it, $attr );
		}
	}
		
    function getColumnWidth( $attr ) 
	{
		if ( $attr != 'Caption' && $attr != 'UID' ) return '8%';
			
		return parent::getColumnWidth( $attr );
	}
}
 