<?php
include_once "SearchableObjectsBuilder.php";

class SearchableObjectsCommonBuilder extends SearchableObjectsBuilder
{
    public function build ( SearchableObjectRegistry $set )
    {
 		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();

 		$set->add( 'Request', array('Caption', 'Description'), 'allissues' );

 		if ( $methodology_it->HasTasks() ) {
     		$set->add( 'Task', array('Caption', 'Comments'), 'currenttasks' );
 		}
 		if ( $methodology_it->get('IsKnowledgeUsed') == 'Y' ) {
     		$set->add( 'ProjectPage', array('Caption', 'Content') );
 		}
 		if ( $methodology_it->HasFeatures() ) {
            $set->add( 'Feature', array('Caption', 'Description'), 'features-trace' );
        }

 		$set->add( 'Question', array('Content'), 'discussions' );
 		$set->add( 'Comment', array('Caption') );
        $set->add( 'Widget', array('Caption','ReferenceName') );
    }
}