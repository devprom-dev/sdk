<?php

include_once "SubversionUserFormEmbedded.php";

class FieldSubversionUser extends FieldForm
{
	private $object_it = null;
	
	function __construct( $object_it )
	{	
		$this->object_it = $object_it;
	}

	function draw( & $view = null )
	{
		$user = getFactory()->getObject('SubversionUser');
		
 		$user->addFilter( 
 				new FilterAttributePredicate( 'Connector', 		 
 					is_object($this->object_it) ? $this->object_it->getId() : 0 )
		);
		
 		$form = new SubversionUserFormEmbedded( $user, 'Connector' );
	 		
	    if ( is_object($this->object_it) ) $form->setObjectIt($this->object_it);
 		    
	    $form->setReadonly( $this->readOnly() );
	 		
	    $form->setTabIndex( $this->getTabIndex() );
	 		
	    $form->draw( $view );
	}

 	function render( & $view )
	{
	    $this->draw( $view );    
	}
}