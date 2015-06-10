<?php

include_once "SearchableObjectsBuilder.php";

class SearchableObjectsCommonBuilder extends SearchableObjectsBuilder
{
    public function build ( SearchableObjectRegistry $set )
    {
 		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
 		
 		$project_it = getSession()->getProjectIt();
 		
 		$set->add( 'Request', array('Caption', 'Description'), 'allissues' );

 		if ( $methodology_it->HasTasks() )
 		{
     		$set->add( 'Task', array('Caption', 'Comments', 'Result'), 'currenttasks' );
 		}
 			
 		if ( $methodology_it->get('IsKnowledgeUsed') == 'Y' )
 		{
     		$set->add( 'ProjectPage', array('Caption', 'Content') );
 		}
 			
 		if ( $project_it->get('IsBlog') == 'Y' )
 		{
     		$set->add( 'BlogPost', array('Caption', 'Content') );
 		}

 		$set->add('Question', array('Content'), 'project-question' );

 		$set->add( 'Comment', array('Caption') );
    }
}