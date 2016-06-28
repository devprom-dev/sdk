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
    			'RequestType' => 'Requests',
     	        'Feature' => 'Features',
     			'FeatureType' => 'Features',
    		    'TaskType' => 'Tasks',
    			'PMBlogPost' => 'Blog',
    			'BlogPostFile' => 'Blog',
     			'Activity' => '3',
				'Attachment' => '3',
     			'Question' => '3',
    			'Comment' => '3',
    			'ChangeLog' => '3',
    			'ChangeLogAggregated' => '3',
    			'SpentTime' => '3',
     	        'Project' => '3',
    			'ProjectPage' => 'KnowledgeBase',
				'CustomTag' => '3',
				'RequestTag' => '3',
				'WikiTag' => '3',
				'Tag' => '3'
    		);
    
		foreach( $entities as $key => $category )
		{
			$set->add( $key, $category );
		} 	    
    }
}