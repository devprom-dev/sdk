<?php
include_once "EmailNotificatorHandler.php";

class ChangeRequestHandler extends EmailNotificatorHandler
{
	function getUsers( $object_it, $prev_object_it, $action )
	{
		$result = array();
		
		switch ( $action )
		{
			case 'modify':
				if ( $prev_object_it->get('State') != $object_it->get('State') ) {
					if ( $object_it->get('Author') > 0 ) {
                        if ( $object_it->getRef('Project')->getMethodologyIt()->get('IsSupportUsed') == 'Y' ) {
                            // don't send email to author inside of support, other handler is used
                            break;
                        }
                        $result[] = $object_it->get('Author');
					}
					if ( $object_it->get('Owner') > 0 ) {
						array_push($result, $object_it->get('Owner'));
					}
				}

				if ( $prev_object_it->get('Owner') != $object_it->get('Owner') ) {
                    if ( $object_it->get('Owner') > 0 ) {
                        array_push($result, $object_it->get('Owner'));
                    }
                }
				break;

			case 'add':
				if ( $object_it->get('Owner') > 0 ) {
					array_push($result, $object_it->get('Owner'));
				}

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
		}
		
		return $result;
	}	
}  
