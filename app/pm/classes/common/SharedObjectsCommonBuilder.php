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
        $requestSection = 'Requests';
        if ( getSession()->IsRDD() ) {
            $requestSection = 'Tasks';
        }
     	$entities = array ( 
            'Request' => $requestSection,
            'RequestTraceBase' => $requestSection,
            'RequestLink' => $requestSection,
            'Feature' => 'Features',
            'FeatureHasIssues' => 'Features',
            'FeatureTerminal' => 'Features',
            'PMBlogPost' => 'Blog',
            'BlogPostFile' => 'Blog',
            'Activity' => '3',
            'ActivityTask' => '3',
            'SpentTime' => '3',
            'ActivityRequest' => '3',
            'Attachment' => '3',
            'Question' => '3',
            'Comment' => '3',
            'ChangeLog' => '3',
            'ChangeLogAggregated' => '3',
            'ChangeLogWhatsNew' => '3',
            'Project' => '3',
            'ProjectPage' => 'KnowledgeBase',
            'CustomTag' => '3',
            'RequestTag' => '3',
            'TaskTag' => '3',
            'QuestionTag' => '3',
            'FeatureTag' => '3',
            'WikiTag' => '3',
            'WikiPageFile' => '3',
            'Tag' => '3',
            'pm_Integration' => '3',
            'pm_TextTemplate' => '3',
            'PMCustomAttribute' => '3',
            'PMCustomAttributeValue' => '3',
            'Snapshot' => '3',
            'Baseline' => '3'
        );
    
		foreach( $entities as $key => $category ) {
			$set->add( $key, $category );
		} 	    
    }
}