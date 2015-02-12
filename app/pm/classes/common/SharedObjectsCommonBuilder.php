<?php

include_once SERVER_ROOT_PATH."pm/classes/common/SharedObjectsBuilder.php";

class SharedObjectsCommonBuilder extends SharedObjectsBuilder
{
    public function getGroup()
    {
        return '';
    }
    
    public function build( SharedObjectRegistry & $set )
    {
     	$entities = array ( 
    			'Request' => 'Requests', 
    			'RequestTraceBase' => 'Requests',
    			'RequestLink' => 'Requests',
    			'IssueState' => 'Requests',
    			'RequestType' => 'Requests',
     	        'Feature' => 'Requests',
     			'FeatureType' => 'Requests',
    		    'TaskType' => 'Requests',
    			'PMBlogPost' => 'Blog',
    			'BlogPostFile' => 'Blog',
     	        'StateBase' => '3',
     			'Activity' => '3',
     			'Question' => '3',
    			'Comment' => '3',
    			'ChangeLog' => '3',
    			'ChangeLogAggregated' => '3',
    			'SpentTime' => '3',
     	        'Project' => '3',
    			'ProjectPage' => 'KnowledgeBase'
    		);
    
		foreach( $entities as $key => $category )
		{
			$set->add( $key, $category );
		} 	    
    }
}