<?php
include_once SERVER_ROOT_PATH."pm/methods/DuplicateWebMethod.php";

class DuplicateIssuesWebMethod extends DuplicateWebMethod
{
	private $type_it = null;

	function __construct( $object_it = null, $type_it = null )
	{
		parent::__construct($object_it);
		$link_type = getFactory()->getObject('RequestLinkType');
		if ( is_object($type_it) ) {
		    $this->type_it = $type_it;
        }
		else {
            $this->type_it = $_REQUEST['LinkType'] != '' ? $link_type->getExact($_REQUEST['LinkType']) : $link_type->getEmptyIterator();
            if ( $this->type_it->getId() < 1 ) {
                $this->type_it = $link_type->getByRef('ReferenceName', 'duplicates');
            }
        }
	}

	function getCaption() {
		return text(2694);
	}

	function getMethodName() {
		return parent::getMethodName().':LinkType';
	}

	function getObject() {
		return getFactory()->getObject('Request');
	}
	
	function getReferences()
	{
		$references = array();
		
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

	function getAttributesToReset()
    {
        $object = $this->getObject();
        $attributes = array_merge(
            $object->getAttributesByGroup('workflow'),
            array(
                'OrderNum'
            )
        );
        foreach( $object->getAttributes() as $attribute => $info ) {
            if ( !$object->IsReference($attribute) ) continue;
            $attributes[] = $attribute;
        }

        $reset = array_diff( $attributes,
            array(
                'Project'
            )
        );
        if ( defined('ISSUE_DUP_PRESERVE_ATTRS') ) {
            $reset = array_diff($reset, ISSUE_DUP_PRESERVE_ATTRS);
        }
        else {
            $reset = array_diff($reset, array('Severity', 'Priority'));
        }
        return $reset;
    }

    function getAttributesDefaults( $iterator )
    {
        $uid = new ObjectUID();
        $override = array(
            'Description' => '{{'.$uid->getObjectUid($iterator).'}}',
            'Author' => getSession()->getUserIt()->getId()
        );
        if ( defined('ISSUE_DUP_PRESERVE_ATTRS') ) {
            foreach( ISSUE_DUP_PRESERVE_ATTRS as $attribute ) {
                unset($override[$attribute]);
            }
        }
        return array_merge(
            parent::getAttributesDefaults($iterator),
            $override
        );
    }

 	function duplicate( $project_it, $parms )
 	{
		$context = parent::duplicate( $project_it, $parms );
 	 	$this->linkIssues($context->getIdsMap());
 	    return $context;
 	}

	function linkIssues( $map )
	{
		$request = getFactory()->getObject('pm_ChangeRequest');
		$link = getFactory()->getObject('pm_ChangeRequestLink');

		foreach( $this->getObjectIt()->idsToArray() as $source_id ) {
		    if ( $map[$request->getEntityRefName()][$source_id] > 0 ) {
                $link->add_parms( array(
                    'SourceRequest' => $source_id,
                    'TargetRequest' => $map[$request->getEntityRefName()][$source_id],
                    'LinkType' => $this->type_it->getId()
                ));
            }
		}
	}
}
