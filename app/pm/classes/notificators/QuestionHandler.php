<?php
include_once "EmailNotificatorHandler.php";

class QuestionHandler extends EmailNotificatorHandler
{
	function getUsers( $object_it, $prev_object_it, $action )
	{
		$result = array();
		
		switch( $action )
		{
			case 'add':
                $leadIt = $this->getProject($object_it)->getLeadIt();
                $result = array_merge($result,
                    $leadIt->object->getRegistry()->Query(
                        array(
                            new FilterInPredicate($leadIt->idsToArray()),
                            new FilterAttributePredicate('NotificationTrackingType', 'system')
                        )
                    )->fieldToArray('SystemUser')
                );
				break;
				
			default:
				if ( $object_it->get('Author') > 0 ) {
					array_push( $result, $object_it->get('Author') );
				}
				break;
		}

        if ( $object_it->get('Owner') != '' ) {
            array_push($result, $object_it->get('Owner'));
        }

		return $result;
	}	
}  
