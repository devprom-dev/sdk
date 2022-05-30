<?php
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";

class DuplicateWebMethod extends WebMethod
{
	private $object_it = null;
	private $result_it = null;
	
	function __construct( $object_it = null )
	{
		$this->setObjectIt( is_object($object_it) 
			? $object_it : getFactory()->getObject('entity')->getEmptyIterator()
		);
		
		parent::__construct();
		$this->setRedirectUrl('devpromOpts.updateUI');
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

	public function setObjectIds( $ids )
    {
        if ( count($ids) < 1 ) {
            $this->setObjectIt($this->getObject()->getEmptyIterator());
        }
        else {
            $this->setObjectIt(
                $this->getObject()->getRegistry()->Query(
                    array(
                        new FilterInPredicate($ids)
                    )
                )
            );
        }
    }

	public function setResult( $result_it ) {
		$this->result_it = $result_it;
	}

	public function getResult() {
		return $this->result_it;
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
 		return "javascript:processBulk('".$this->getCaption()."','?formonly=true&operation=".$this->getMethodName()."&".http_build_query($parms)."', ".$id.", ".$this->getRedirectUrl().")";
	}
	
	function getReferences() {
		return array();
	}

	function getAttributesToReset() {
	    return array();
    }

    function getAttributesDefaults( $iterator ) {
	    return array();
    }

	function getObject()
	{
	    if ( is_object($this->object_it) ) return $this->object_it->object;
		$class = getFactory()->getClass($_REQUEST['class']);
		if ( !class_exists($class) ) return null;
		return getFactory()->getObject($class);
	}
	
	function getIdsMap( & $object )
	{
		return array();
	}
	
 	function execute_request()
 	{
        $this->execute( $_REQUEST );
 	}

 	function getTargetIt( $parms )
    {
        $project = getFactory()->getObject('Project');

        $target_it = $parms['Project'] > 0
            ? $project->getExact( $parms['Project'] )
            : $project->getEmptyIterator();

        return $target_it;
    }

 	function execute( $parms )
    {
        if ( $this->object_it->getId() == '' ) {
            $ids = \TextUtils::parseIds($parms['objects']);
        }
        else {
            $ids = $this->object_it->idsToArray();
        }
        $this->setObjectIds($ids);

        $target_it = $this->getTargetIt($parms);

        $context = $this->duplicate( $target_it, $parms );

        $object_it = $this->getObjectIt();

        $map = $context->getIdsMap();

        $duplicates = array();

        $ref_name = $object_it->object->getEntityRefName();

        foreach( $object_it->idsToArray() as $id ) {
            $duplicates[] = $map[$ref_name][$id];
        }

        if ( count($duplicates) > 0 ) {
            $duplicate_it = $object_it->object->getExact($duplicates);

            $duplicate_it->moveFirst();
            $this->setResult($duplicate_it);

            if ( $parms['OpenList'] != '' && $duplicate_it->count() > 0 ) {
                if ( $duplicate_it->count() == 1 ) {
                    $this->setRedirectUrl($duplicate_it->getUidUrl());
                }
                else {
                    $this->setRedirectUrl(
                        getFactory()->getObject('PMReport')->getExact('allissues')->getUrl(
                            'ids='.\TextUtils::buildIds($duplicate_it->idsToArray())
                        )
                    );
                }
            }
            elseif( $duplicate_it->count() == 1 ) {
                $this->setRedirectUrl($duplicate_it->getUidUrl());
            }
        }
    }

 	function duplicate( $project_it, $parms )
 	{
 	    global $session;

 	    // prepare list of objects to be serilalized
 	    $references = $this->getReferences();
        $ids_map = array();

 	    $xml = '<?xml version="1.0" encoding="'.APP_ENCODING.'"?><entities>';
 	    foreach( $references as $object ) {
 	       $xml .= $object->serialize2Xml();
 	       $ids_map = array_merge($ids_map, $this->getIdsMap($object));
 	    }
 	    $xml .= '</entities>';

 	    $object_it = $this->getObjectIt();

 	    $context = $this->buildContext();
 	    $context->setIdsMap( $ids_map );
        $context->setUseExistingReferences( true );
        $context->setRestoreFromTemplate(false);
        $context->setRaiseExceptions(true);

 	    if ( $project_it->getId() == '' ) {
            // bind data to existing objects if any
            $context->setReuseProject(true);
        }
        else {
            $session = new PMSession( $project_it );
        }

 	    foreach( $references as $object )
 	    {
 	        $object = getFactory()->getObject( get_class($object) );
 	        $iterator = $object->createXMLIterator($xml);

 	        if ( get_class($object_it->object) == get_class($object) )
 	        {
 	            $rowset = array();
                $toReset = array_merge($this->getAttributesToReset(), array_keys($parms));
 	            while( !$iterator->end() ) {
                    $defaults = $this->getAttributesDefaults($iterator);
                    foreach( $object->getAttributes() as $attribute => $info ) {
                        if ( in_array($attribute, $toReset) ) {
                            $defaults[$attribute] = $parms[$attribute];
                        }
                    }
                    $rowset[] = array_merge($iterator->getData(), $defaults);
                    $iterator->moveNext();
                }
 	        	$iterator = $object->createCachedIterator($rowset);
 	        }

 	        CloneLogic::Run( $context, $object, $iterator, $project_it);
 	    }

 	    return $context;
 	}
}