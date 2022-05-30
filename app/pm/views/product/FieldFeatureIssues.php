<?php

include "FormFeatureIssuesEmbedded.php";

class FieldFeatureIssues extends FieldForm
{
 	var $object_it;
 	private $issueIt = null;
 	private $requestObject = null;
 	
 	function __construct( $object_it, $requestObject ) {
 		$this->object_it = $object_it;
 		$this->requestObject = $requestObject;
 	}

 	function setIssueIt( $value ) {
 	    $this->issueIt = $value;
    }

 	function render( $view ) {
	    $this->draw( $view );    
	}
 	
 	function draw( $view = null )
 	{
 		$task = $this->requestObject;
 		$task->disableVpd();

 		$task->addFilter( new FilterAttributePredicate( 'Function',
 			is_object($this->object_it) ? $this->object_it->getId() : '-1' ) );
 		
 		$task->addSort( new SortAttributeClause('State') );
 		$task->addSort( new SortOrderedClause() );
 		$task->addSort( new SortKeyClause() );
 		
 		echo '<div id="'.$this->getId().'" class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';
 		    $form = new FormFeatureIssuesEmbedded( $task, 'Function' );
 		    if ( is_object($this->object_it) ) $form->setObjectIt($this->object_it);
 		    $form->setReadonly( $this->readOnly() );
 		    $form->setTabIndex( $this->getTabIndex() );
	 		
 		    $form->draw( $view );
 		echo '</div>';

 		if ( is_object($this->issueIt) && $this->issueIt->getId() > 0 ) {
 		    echo '<br/>';
 		    echo '<div>';
                $uid = new ObjectUID();
                $uid->drawUidInCaption($this->issueIt);
            echo '</div>';
        }
 	}
} 