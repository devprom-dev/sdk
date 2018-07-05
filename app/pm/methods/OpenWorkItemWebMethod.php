<?php
use Devprom\ProjectBundle\Service\Workflow\WorkflowService;
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";

class OpenWorkItemWebMethod extends WebMethod
{
    private $objectIt = null;

    function __construct( $objectIt ) {
        $this->objectIt = $objectIt;
    }

    function getModule() {
        return getSession()->getApplicationUrl($this->objectIt).'methods.php';
    }

    function execute_request()
 	{
 	    $className = getFactory()->getClass($_REQUEST['class']);
 	    if ( !class_exists($className) ) throw new Exception('Unknown class name given');

 	    $objectIt = getFactory()->getObject($className)->getExact($_REQUEST['object']);
 	    if ( $objectIt->getId() == '' ) throw new Exception('Unknown object given');

        $stateIt = $objectIt->getStateIt();
        if ( $stateIt->get('IsTerminal') == 'N' ) {
            $nextStateIt = getFactory()->getObject($objectIt->object->getStateClassName())->getRegistry()->Query(
                array(
                    new FilterAttributePredicate('IsTerminal', 'I'),
                    new FilterVpdPredicate($objectIt->get('VPD'))
                )
            );
            if ( $nextStateIt->getId() > 0 ) {
                $service = new WorkflowService($objectIt->object);
                $service->moveToState($objectIt, $nextStateIt->get('ReferenceName'));
            }
            else {
                throw new Exception('State with base "In progress" was not found for '.$nextStateIt->getId().'"');
            }
        }
        else {
            if ( $objectIt->get('StateObject') == '' ) {
                $parms = array(
                    'State' => getFactory()->getObject($objectIt->object->getStateClassName())->getRegistry()->Query(
                        array(
                            new FilterAttributePredicate('IsTerminal', 'I'),
                            new FilterVpdPredicate($objectIt->get('VPD'))
                        )
                    )->get('ReferenceName')
                );
                $objectIt->object->moveToState($objectIt, $parms);
                if ( $parms['StateObject'] > 0 ) {
                    DAL::Instance()->Query(
                        " UPDATE ".$objectIt->object->getEntityRefName()." SET StateObject = ".$parms['StateObject']." WHERE ".$objectIt->object->getIdAttribute()." = ".$objectIt->getId()
                    );
                }
            }
            else {
                DAL::Instance()->Query(" UPDATE pm_StateObject SET RecordModified = NOW() WHERE pm_StateObjectId = ".$objectIt->get('StateObject'));
            }
        }

        echo $objectIt->getViewUrl();
	}
}