<?php

include "WikiTemplateList.php";

class WikiTemplateTable extends PMPageTable
{
	private $form;
	
 	function __construct( & $object, & $form )
 	{
 		$this->form = $form;
 		
 		parent::__construct( $object );
 	}
 	
 	function & getForm()
 	{
 	    return $this->form;
 	}
 	
	function getFilterActions()
	{
		return array();
	}

	function getDeleteActions()
	{
		return array();
	}
	
	function & getObjectIt()
	{
	    if ( is_object($this->object_it) ) return $this->object_it;
	    
	    $key = 'object';
	    
	    if ( $_REQUEST[$key] != '' )
	    {
	        $this->object_it = $this->getObject()->getExact($_REQUEST[$key]);
	    }
	    else
	    {
	        $this->object_it = $this->getObject()->getEmptyIterator();
	    }
	    
	    return $this->object_it;
	}
	
 	function getRenderParms( $parms )
 	{
		$parent_parms = parent::getRenderParms( $parms );
 	    
		$form_parms = $this->getForm()->getRenderParms();
 	    
		return array_merge( $parent_parms, array (
 	        'scripts' => $form_parms['scripts'],
		    'object_id' => $this->getObjectIt()->getId() 
 	    ));
 	}
 	
	function getList( $type = '', $iterator = null )
	{
	    $list = new WikiTemplateList( $this->getObject(), $iterator );
	    
	    $list->setInfiniteMode();
	    
	    return $list;
	}
	
	function getTemplate()
	{
	    return 'pm/WikiDocument.php';
	}

	protected function buildFilterWatcher()
	{
	}
} 
