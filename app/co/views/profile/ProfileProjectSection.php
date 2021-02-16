<?php
include_once SERVER_ROOT_PATH . 'co/classes/persisters/ParticipantRoleNamesPersister.php';

class ProfileProjectSection extends InfoSection
{
 	private $object_it;
 	
 	function __construct( $object_it ) {
 		$this->object_it = $object_it;
 		parent::__construct();
 	}
 	
 	function getCaption() {
 		return translate('Мои проекты');
 	}
 	
 	function getObjectIt() {
 		return $this->object_it;
 	}
 	
 	function drawBody()
	{
	    $notificationIt = getFactory()->getObject('Notification')->getAll();
        $partIt = getFactory()->getObject('Participant')->getRegistry()->Query(
            array(
                new FilterAttributePredicate('SystemUser', $this->object_it->getId()),
                new SortAttributeClause('Project'),
                new ParticipantRoleNamesPersister(),
                new ParticipantActivePredicate()
            )
        );
        while( !$partIt->end() ) {
            $projectIt = $partIt->getRef('Project');
            echo '<a href="/pm/'.$projectIt->get('CodeName').'/profile">'.$projectIt->getDisplayName().'</a>';
            if ( defined('PERMISSIONS_ENABLED') && PERMISSIONS_ENABLED ) {
                echo '<br/>';
                echo translate('Роли') . ': ' . join(', ', array_filter(
                    preg_split('/,/', $partIt->get('ProjectRoleName')),
                    function($value) {
                        return trim($value) != '';
                    }
                ));
            }
            $notificationIt->moveToId($partIt->get('NotificationEmailType'));
            if ( $notificationIt->getId() != '' ) {
                echo '<br/>';
                echo $notificationIt->getDisplayName();
            }
            echo '<br/><br/>';
            $partIt->moveNext();
        }
    }
}
