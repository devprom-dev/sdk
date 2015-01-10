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
	
	function getLink( $project_id = '' )
	{
		$url = '?mode=bulk&ids='.$this->getObjectIt()->getId().
					'&bulkmode=complete&operation='.$this->getMethodName().'&redirect='.urlencode($_SERVER['REQUEST_URI']);
		if ( $project_id != '' )
		{
			$url .= '&Project='.$project_id;
		}
		return $url;
	}
	
	function getJSCall( $parms = array() )
	{
 		return "javascript: processBulkMethod('".$this->getMethodName()."'); ";
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
 	    
 	    $xml = '<?xml version="1.0" encoding="windows-1251"?><entities>';
 	    
 	    $ids_map = array();
 	    
 	    foreach( $references as $object )
 	    {
 	       $xml .= $object->serialize2Xml();
 	       
 	       $ids_map = array_merge($ids_map, $this->getIdsMap($object));
 	    }
 	    
 	    $xml .= '</entities>';
  
 	    $source_ids = $this->getObjectIt()->idsToArray();

 	    // duplicate serialized data in the target project
 	    $session = new PMSession( $project_it );
 	    
 	    $context = $this->buildContext();
 	    
 	    $context->setIdsMap( $ids_map );

 	    // bind data to existing objects if any
 	    $context->setUseExistingReferences( true );
 	     	    
 	    foreach( $references as $object )
 	    {
 	        $object = $model_factory->getObject( get_class($object) );

     	    CloneLogic::Run( $context, $object, $object->createXMLIterator($xml), $project_it);
 	    }

 	    return $context;
 	}
}