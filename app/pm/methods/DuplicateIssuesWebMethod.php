<?php

include_once SERVER_ROOT_PATH."pm/methods/DuplicateWebMethod.php";

class DuplicateIssuesWebMethod extends DuplicateWebMethod
{
	private $type_it = null;

	function __construct( $object_it = null )
	{
		parent::__construct($object_it);
		$link_type = getFactory()->getObject('RequestLinkType');
		$this->type_it = $_REQUEST['LinkType'] != '' ? $link_type->getExact($_REQUEST['LinkType']) : $link_type->getEmptyIterator();
		if ( $this->type_it->getId() < 1 ) {
			$this->type_it = $link_type->getByRef('ReferenceName', 'duplicates');
		}
	}

	function getCaption()
	{
		return text(867);
	}

	function getMethodName()
	{
		return parent::getMethodName().':LinkType:OpenList';
	}

	function getObject()
	{
		return getFactory()->getObject('Request');
	}
	
	function getReferences()
	{
		$references = array();
		
 	    $references[] = getFactory()->getObject('pm_IssueType');
 	    $references[] = getFactory()->getObject('Priority');

 	    $request = getFactory()->getObject('pm_ChangeRequest');
 	    $request->addFilter( new FilterInPredicate($this->getObjectIt()->idsToArray()) );
   	    $references[] = $request;
 	    
 	    if ( $this->type_it->get('ReferenceName') == 'duplicates' ) {
	 	    $trace = getFactory()->getObject('pm_ChangeRequestTrace');
			$trace->addFilter( new FilterAttributePredicate('ChangeRequest', $this->getObjectIt()->idsToArray()) );
			$references[] = $trace;
 	    }

		return $references;
	}
	
 	function duplicate( $project_it )
 	{
		$context = parent::duplicate( $project_it );
 	 	$this->linkIssues($context->getIdsMap());
 	    return $context;
 	}

	function linkIssues( $map )
	{
		$request = getFactory()->getObject('pm_ChangeRequest');
		$link = getFactory()->getObject('pm_ChangeRequestLink');

		foreach( $this->getObjectIt()->idsToArray() as $source_id ) {
			$link->add_parms( array(
				'SourceRequest' => $source_id,
				'TargetRequest' => $map[$request->getEntityRefName()][$source_id],
				'LinkType' => $this->type_it->getId()
			));
		}
	}
}
