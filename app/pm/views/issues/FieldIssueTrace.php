<?php

include_once SERVER_ROOT_PATH."pm/views/ui/ObjectTraceFormEmbedded.php";

class FieldIssueTrace extends FieldForm
{
 	private $task_it = null;
 	private $trace = null;
 	private $type = '';
 	
 	function __construct( $task_it, $trace, $type = '' )
 	{
 		$this->object_it = $task_it;
 		$this->trace = $trace;
 		$this->type = $type;
 	}

 	function getObjectIt()
 	{
 		return $this->object_it;
 	}
 	
 	function draw( $view = null )
	{
		echo '<div class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';
			$this->drawBody();
		echo '</div>';
	}
	
 	function getFilters()
 	{
		return array ( 
			new FilterAttributePredicate( 'ChangeRequest', is_object($this->object_it) ? $this->object_it->getId() : 0 )
		);
 	}
 	
 	function getForm( & $trace )
	{
		return new ObjectTraceFormEmbedded( $trace, 'ChangeRequest', $this->getName() );
	}
	
 	function render( $view )
	{
	    $this->drawBody( $view );    
	}
	
	function drawBody( $view = null )
	{
		$filters = $this->getFilters();
		
		if ( $this->type != '' ) $filters[] = new FilterAttributePredicate('Type', $this->type); 
		
		$this->trace->disableVpd();
		
		foreach( $filters as $filter )
		{
			$this->trace->addFilter($filter);
		}
		
 		$form = $this->getForm($this->trace);
 		
 		$form->setTraceObject( getFactory()->getObject($this->trace->getObjectClass()) );
 		
 		$form->setTraceType( $this->type );
 			
		$object_it = $this->getObjectIt();
		
 		if ( is_object($object_it) )
 		{
 			if ( !$this->getEditMode() ) $form->setObjectIt( $object_it );
 		}
 		
 		$form->setReadonly( $this->readOnly() );
 		
 		$form->setTabIndex( $this->getTabIndex() );
 			
 		echo '<div>';
	 		$form->draw( $view );
 		echo '</div>';
	}
}