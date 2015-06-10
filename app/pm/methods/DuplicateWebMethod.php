<?php

include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";

class DuplicateWebMethod extends WebMethod
{
	private $object_it = null;
	
	private $context = null;

	function __construct( $object_it = null )
	{
		$this->setObjectIt( is_object($object_it) 
			? $object_it : getFactory()->getObject('entity')->getEmptyIterator()
		);
		
		parent::__construct();
		$this->setRedirectUrl('function(){window.location.reload();}');
	}
	
	protected function buildContext()
	{
		return new CloneContext(); 
	}
	
	public function & getObjectIt()
	{
		$this->object_it->moveFirst();
		
		return $this->object_it;
	}
	
	public function setObjectIt( $object_it )
	{
		$this->object_it = $object_it;
	}

 	function hasAccess()
 	{
 		return getFactory()->getAccessPolicy()->can_create($this->getObjectIt()->object) || getSession()->getProjectIt()->IsPortfolio();
 	}
	
	function getCaption() 
	{
		return '';
	}

	function getMethodName()
	{
		return 'Method:'.get_class($this).':Project';
	}
	
	function getJSCall( $parms = array() )
	{
		$id = $this->object_it->getId() != '' ? $this->object_it->getId() : '0';
 		return "javascript:processBulk('".$this->getCaption()."','?formonly=true&operation=".$this->getMethodName()."&Project=".$parms['Project']."', ".$id.", ".$this->getRedirectUrl().")";
	}
	
	function getReferences()
	{
		return array();
	}
	
	function getIdsMap( & $object )
	{
		return array();
	}
	
 	function execute_request()
 	{
 		if ( $this->object_it->getId() == '' )
 		{
 			$class = getFactory()->getClass($_REQUEST['class']);
 			
 			if ( !class_exists($class) ) return;
 			
 			$this->setObjectIt(
 					$_REQUEST['objects'] != '' 
 						? getFactory()->getObject($class)->getExact(preg_split('/-/', $_REQUEST['objects']))
 						: getFactory()->getObject($class)->getEmptyIterator()
 			);
 		}
 		
		$project = getFactory()->getObject('Project');
		
		$target_it = $_REQUEST['Project'] > 0 ? $project->getExact( $_REQUEST['Project'] ) : $project->getEmptyIterator();
		
		if ( $target_it->getId() < 1 ) $target_it = getSession()->getProjectIt();
	    
	    $context = $this->duplicate( $target_it );
	    
	    $object_it = $this->getObjectIt();

    	$map = $context->getIdsMap();
    	
    	$duplicates = array();
    	
    	$ref_name = $object_it->object->getEntityRefName();
    	
    	foreach( $object_it->idsToArray() as $id )
    	{
    		$duplicates[] = $map[$ref_name][$id]; 
    	}
    	
    	$duplicate_it = $object_it->object->getExact($duplicates);
	    
	    getFactory()->getEventsManager()->
	    		executeEventsAfterBusinessTransaction($duplicate_it->copyAll(), 'WorklfowMovementEventHandler');

	    if ( $duplicate_it->count() > 1 ) return;
	    
    	// for single object automatically redirect to the duplicate
    	
	    $duplicate_it->moveFirst();
	    
    	$this->setRedirectUrl( $duplicate_it->getViewUrl() );
 	}
 	
 	function duplicate( $project_it )
 	{
 	    global $model_factory, $session;
 	    
 	    // prepare list of objects to be serilalized
 	    $references = $this->getReferences();
 	    
 	    $xml = '<?xml version="1.0" encoding="'.APP_ENCODING.'"?><entities>';
 	    
 	    $ids_map = array();
 	    
 	    foreach( $references as $object )
 	    {
 	       $xml .= $object->serialize2Xml();
 	       
 	       $ids_map = array_merge($ids_map, $this->getIdsMap($object));
 	    }
 	    
 	    $xml .= '</entities>';
  
 	    $object_it = $this->getObjectIt();
 	    $source_ids = $object_it->idsToArray();

 	    // duplicate serialized data in the target project
 	    $session = new PMSession( $project_it );
 	    
 	    $context = $this->buildContext();
 	    $context->setIdsMap( $ids_map );
 	    // bind data to existing objects if any
 	    $context->setUseExistingReferences( true );

 	    foreach( $references as $object )
 	    {
 	        $object = getFactory()->getObject( get_class($object) );
 	        $iterator = $object->createXMLIterator($xml);
 	        
 	        if ( get_class($object_it->object) == get_class($object) )
 	        {
 	        	// override entity values with user ones
 	        	$defaults = array();
 	        	foreach( $object->getAttributes() as $attribute => $info ) {
 	        		if ( $_REQUEST[$attribute] != '' ) {
 	        			$defaults[$attribute] = $_REQUEST[$attribute]; 
 	        		}
 	        	}
 	        	$iterator->setData(array_merge($iterator->getData(), $defaults));
 	        }

     	    CloneLogic::Run( $context, $object, $iterator, $project_it);
 	    }

 	    return $context;
 	}
}