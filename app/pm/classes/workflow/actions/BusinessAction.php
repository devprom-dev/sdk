<?php
use Devprom\ProjectBundle\Service\Model\ModelService;
use Devprom\ProjectBundle\Service\Workflow\WorkflowService;

class BusinessAction
{
 	function getId()
 	{
 		return null;
 	}
 	
 	function getDisplayName()
 	{
 		return '';
 	}
 	
 	function getObject()
 	{
 		return null;
 	}
 	
 	function apply( $object_it )
 	{
 		return false;
 	}

 	function getData() {
        return $this->data;
    }

    function setData( $data ) {
        $this->data = $data;
    }

    function process( $action_it, $object_it )
    {
        $parms = array();

        foreach($action_it->object->getActionAttributes() as $attribute)
        {
            if ( $action_it->get($attribute) == '' ) continue;
            $parms[$attribute] = $action_it->getHtmlDecoded($attribute);
        }

        foreach( \TextUtils::parseItems($action_it->get('ResetAttributes')) as $attribute ) {
            $parms[$attribute] = 'NULL';
        }

        return $this->modify($action_it, $object_it, $parms);
    }

    function modify ( $action_it, $object_it, $parms )
    {
        $errors = array();

        if ( count($parms) > 0 ) {
            if ( $parms['State'] != '' ) {
                $service = new WorkflowService($object_it->object);
                if ( !$service->moveToState( $object_it->copy(), $parms['State'], '', $parms ) ) {
                    $errors[] = text(2832);
                }
            }
            else {
                if ( $object_it->object->modify_parms( $object_it->getId(), $parms ) < 1 ) {
                    $errors[] = text(2833);
                }
            }
        }

        $taskParms = array_filter( $parms,
            function($value, $key) {
                return strpos($key, 'Task_') !== false;
            },
            ARRAY_FILTER_USE_BOTH
        );

        if ( count($taskParms) > 0 ) {
            $values = array(
                'Author' => getSession()->getUserIt()->getId()
            );
            foreach( $taskParms as $parm => $value ) {
                $parm = str_replace('Task_', '', $parm);
                $values[$parm] = $value;
            }

            $values['ChangeRequest'] = $object_it->getId();
            if ( getFactory()->getObject('Task')->add_parms($values) < 1 ) {
                $errors[] = text(2834);
            }
        }

        if ( $action_it->get('NewComment') != '' ) {
            $comment = getFactory()->getObject('Comment');
            $commentIt = $comment->getRegistry()->Create(
                array(
                    'ObjectId' => $object_it->getId(),
                    'ObjectClass' => get_class($object_it->object),
                    'Caption' => $action_it->getHtmlDecoded('NewComment'),
                    'AuthorId' => getSession()->getUserIt()->getId()
                )
            );
            if ( $commentIt->getId() == '' ) {
                $errors[] = text(2835);
            }
        }

        $modifiedIt = $object_it->object->getRegistry()->Query(
            array( new FilterInPredicate($object_it->getId()) )
        );

        $actionParms = array(
            'AutoActionUserName' => $action_it->getDisplayName()
        );
        if ( count($errors) > 0 ) {
            $actionParms['AutoActionErrors'] = join(', ', $errors);
        }

        $notificator = new PMChangeLogNotificator();
        $notificator->setRecordData( $actionParms );
        $notificator->modify( $object_it, $modifiedIt );

        return true;
    }

    protected function checkConditions( $action_it, $object_it )
    {
        return ModelService::queryXPath(
                $object_it->copyAll(),
                $action_it->getConditionXPath()
            )->count() > 0;
    }

 	private $data = array();
}
