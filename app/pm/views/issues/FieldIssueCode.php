<?php
include_once SERVER_ROOT_PATH."pm/classes/plan/validators/ModelValidatorDeadline.php";
include_once "FieldIssueTrace.php";

class FieldIssueCode extends FieldIssueTrace
{
 	function __construct( $object_it ) {
 		parent::__construct($object_it, getFactory()->getObject('RequestTraceSourceCode'));
 	}

    function drawBody( $view = null ) {
         parent::drawBody($view);

        $object_it = $this->getObjectIt();
        if ( is_object($object_it) && $object_it->get('Tasks') != '' ) {
            $traceIt = getFactory()->getObject('TaskTraceSourceCode')->getRegistry()->Query(
                array(
                    new FilterAttributePredicate('Task', $object_it->get('Tasks'))
                )
            );
            echo '<p></p><p>'.text(3324).'</p>';
            while( !$traceIt->end() ) {
                echo '<div>';
                    echo $traceIt->getDisplayName();
                echo '</div>';
                $traceIt->moveNext();
            }
        }
    }
}