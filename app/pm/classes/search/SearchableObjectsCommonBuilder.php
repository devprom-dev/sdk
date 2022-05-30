<?php
include_once "SearchableObjectsBuilder.php";

class SearchableObjectsCommonBuilder extends SearchableObjectsBuilder
{
    public function build ( SearchableObjectRegistry $set )
    {
 		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();

 		$set->add( 'Request', 'allissues' );

 		if ( $methodology_it->HasTasks() ) {
     		$set->add( 'Task', 'currenttasks' );
 		}
 		if ( $methodology_it->get('IsKnowledgeUsed') == 'Y' ) {
     		$set->add( 'ProjectPage');
 		}
 		if ( $methodology_it->HasFeatures() ) {
            $set->add( 'Feature', 'features-trace' );
        }

 		$set->add( 'Question', 'discussions' );
 		$set->add( 'Comment');
        $set->add( 'Component');
        $set->add( 'Widget');
    }
}