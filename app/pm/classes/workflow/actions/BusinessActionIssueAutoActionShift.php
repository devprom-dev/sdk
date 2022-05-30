<?php
include_once SERVER_ROOT_PATH."pm/classes/workflow/actions/BusinessActionShift.php";

class BusinessActionIssueAutoActionShift extends BusinessActionShift
{
	private $action_it = null;
	
	function __construct( $action_it ) {
		$this->action_it = $action_it;
	}
	
 	function getId() {
 		return $this->action_it->get('ReferenceName');
 	}

 	function getDisplayName() {
 		return text(2434).': '.$this->action_it->getDisplayName();
 	}
 	
 	function getObject() {
 		return getFactory()->getObject('Request');
 	}
 	
	function applyContent( $object_it, $attributes, $action = '' )
 	{
 	    $this->setData($attributes);

        $checkEventType =
            $action == TRIGGER_ACTION_ADD && in_array(
                $this->action_it->get('EventType'),
                array(AutoActionEventRegistry::CreateOnly,AutoActionEventRegistry::CreateAndModify)
            ) ||
            $action == TRIGGER_ACTION_MODIFY && in_array(
                $this->action_it->get('EventType'),
                array(AutoActionEventRegistry::ModifyOnly,AutoActionEventRegistry::CreateAndModify)
            ) ||
            $action == '';

 	    if ( !$checkEventType ) return;

 	    $actionAttributes = array_flip($this->action_it->getConditionAttributes());

 	    $watchForChanges =
            in_array(
                $this->action_it->get('EventType'),
                array(AutoActionEventRegistry::ModifyOnly,AutoActionEventRegistry::CreateAndModify)
            );
 	    if ( $watchForChanges && count(array_intersect_key($actionAttributes, $attributes)) < 1 ) return false;

        $object_it = $object_it->object->createCachedIterator(
            array(
                $watchForChanges
                    ? array_merge(
                            array(
                                $object_it->object->getIdAttribute() => $object_it->getId(),
                                'VPD' => $object_it->get('VPD')
                            ),
                            array_intersect_key(
                                array_merge(
                                    $object_it->getData(),
                                    $attributes
                                ),
                                $actionAttributes
                            )
                        ) // react on changes only
                    : array_merge(
                            $object_it->getData(),
                            $attributes
                        ) // react on entity data
            )
        );
 		if ( !$this->checkConditions($this->action_it, $object_it) ) return false;
 		return $this->process($this->action_it, $object_it);
 	}
}
