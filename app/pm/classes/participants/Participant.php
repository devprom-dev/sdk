<?php

include_once SERVER_ROOT_PATH."pm/classes/communications/Notification.php";

include "ParticipantIterator.php";
include "predicates/ParticipantActivePredicate.php";
include "predicates/ParticipantBaseRoleNamePredicate.php";
include "predicates/ParticipantBaseRolePredicate.php";
include "predicates/ParticipantIterationInvolvedPredicate.php";
include "predicates/ParticipantRolePredicate.php";
include "predicates/ParticipantTeamAtWorkPredicate.php";
include "predicates/ParticipantUserGroupPredicate.php";
include "predicates/ParticipantUserPredicate.php";
include "predicates/ParticipantWorkerPredicate.php";
include "predicates/ParticipantWorkloadPredicate.php";
include "predicates/UserWorkerPredicate.php";
include "persisters/ParticipantDetailsPersister.php";
include "persisters/ParticipantOthersPersister.php";
include "persisters/ParticipantRolesPersister.php";
include "persisters/UserParticipatesDetailsPersister.php";

class Participant extends Metaobject
{
 	function __construct( $registry = null ) 
 	{
		parent::__construct('pm_Participant', $registry);
		
		$this->defaultsort = " IFNULL(IsActive, 'N') DESC, Caption ASC ";
		
		$this->addAttribute('ParticipantRole', 'REF_ParticipantRoleId', translate('Роль в проекте'), false, false, '', 100);
		
		$this->addAttribute('ProjectRole', 'REF_ProjectRoleId', translate('Роль'), false, false, '', 101);

		$this->setAttributeRequired('ProjectRole', true);
		
		$this->addPersister( new ParticipantRolesPersister() );
		
		$this->setAttributeCaption('Capacity', translate('Ежедневная загрузка, ч.'));
		
		$this->setAttributeOrderNum('Capacity', 102);
		
		$this->setAttributeRequired('Capacity', true);
		
		$this->addAttribute('Notification', 'VARCHAR', 'text(964)', false, false, '', 103);
		
		$this->addPersister( new ParticipantDetailsPersister() );
		
		$system_attributes = array (
		        'Login',
		        'Password',
		        'OverrideUser',
		        'Salary'
		);
		
		foreach( $system_attributes as $attribute )
		{
			$this->addAttributeGroup($attribute, 'system');
		    
		    $this->setAttributeVisible($attribute, false);
		}
	}
	
	function extendMetadata()
	{
	}

	function createIterator() 
	{
		return new ParticipantIterator( $this );
	}

	function add_parms( $parms ) 
	{
		$user_it = getFactory()->getObject('cms_User')->getExact($parms['SystemUser']);
		if($user_it->count() > 0) 
		{
			$parms['Caption'] = $user_it->get('Caption');
			$parms['Login'] = $user_it->get('Login');
			$parms['Email'] = $user_it->get('Email');
			$parms['HomePhone'] = $user_it->get('Phone');
			$parms['ICQNumber'] = $user_it->get('ICQ');
			$parms['Skype'] = $user_it->get('Skype');
		}

		$part_id = parent::add_parms( $parms );
	
		$setting = getFactory()->getObject('pm_UserSetting');
		$setting->add_parms(
				array (
						'Setting' => md5('emailnotification'),
						'Value' => 'email='.$parms['Notification'],
						'Participant' => $part_id,
						'VPD' => $parms['VPD'] 
				)
		);
		
		return $part_id;
	}
	
	function modify_parms( $object_id, $parms, $b_notification = true ) 
	{
		global $model_factory;
		
		if ( $parms['IsActive'] != 'on' )
		{
			$part_it = $this->getExact($object_id);
			
			if ( !getFactory()->getAccessPolicy()->can_delete($part_it) )
			{
				$parms['IsActive'] = 'Y';
			}
		}
		
		$result = parent::modify_parms( $object_id, $parms, $b_notification );
		if ( $result < 1 ) return $result;

		// change email notifications settings
		$part = $model_factory->getObject('pm_Participant');
		$part_it = $part->getExact( $object_id );

		$notification = $model_factory->getObject('Notification');
		$notification->store( $parms['Notification'], $part_it );
		
		return $result;
	}

	function DeletesCascade( $object )
	{
	    switch ( $object->getEntityRefName() )
	    {
	        case 'pm_ParticipantMetrics':
	        case 'pm_ParticipantRole':
	            return true;
                	                
	        default:
	            return false;
	    }
	}
	
	function IsDeletedCascade( $object )
	{
	    if ( is_a($object, 'User') ) return true;
	    
	    return false;
	}
	
	function getPage() 
	{
		$session = getSession();
		
		return $session->getApplicationUrl().'participants/list?';
	}
 
	function getAttributeUserName( $name ) 
	{
		if($name == 'RepeatPassword') return 'Повтор пороля';
		return parent::getAttributeUserName( $name );
	}
	
	function getFactByDays( $participant_id, $period_begin, $period_end )
	{
		$sql = "SELECT SUM(Fact / (TO_DAYS(RecordModified) - TO_DAYS(RecordCreated) + 1)) Fact, RecordModified, ".
			   "	   TO_DAYS(RecordCreated) - TO_DAYS('".$period_begin."') BeginDay,  ".
			   "	   TO_DAYS(RecordModified) - TO_DAYS('".$period_begin."') EndDay  ".
			   "  FROM pm_Task WHERE Assignee = ".$participant_id.
			   "   AND RecordCreated >= '".$period_begin." 00:00:00' AND RecordModified <= '".$period_end." 23:59:59'".
			   " GROUP BY BeginDay, EndDay ".
			   " ORDER BY BeginDay, EndDay ASC ";

		return $this->createSQLIterator($sql);
	}
	
	function getTaskCountByTypes( $participant_id, $period_begin, $period_end )
	{
		$sql = "SELECT COUNT(1) TaskCount, TaskType ".
			   "  FROM pm_Task WHERE Assignee = ".$participant_id.
			   "   AND RecordModified BETWEEN '".$period_begin." 00:00:00' AND '".$period_end." 23:59:59' ".
			   " GROUP BY TaskType ".
			   " ORDER BY TaskType DESC ";

		return $this->createSQLIterator($sql);
	}

	function getSumFactByPeriod( $participant_id, $period_begin, $period_end )
	{
		$sql = "SELECT SUM(Fact) SumFact, ".
			   "	   TO_DAYS('".$period_end." 23:59:59') - TO_DAYS('".$period_begin." 00:00:00') PeriodDays  ".
			   "  FROM pm_Task WHERE Assignee = ".$participant_id.
			   "   AND RecordModified BETWEEN '".$period_begin." 00:00:00' AND '".$period_end." 23:59:59' ";

		return $this->createSQLIterator($sql);
	}
	
	function getDevelopmentTeam()
	{
		global $project_it;
		
		$sql = "SELECT p.* ".
			   "  FROM pm_Participant p " .
			   " WHERE EXISTS (SELECT 1 FROM pm_ParticipantRole r, pm_ProjectRole l" .
			   "				WHERE r.Participant = p.pm_ParticipantId " .
			   "                  AND r.ProjectRole = l.pm_ProjectRoleId" .
			   "				  AND l.ReferenceName NOT IN ('client', 'guest') ) " .
			   "   AND p.IsActive = 'Y' ".
			   "   AND p.Project = ".$project_it->getId().
			   " ORDER BY p.Caption ASC ";

		return $this->createSQLIterator($sql);
	}

	function getDeveloperIt()
	{
		global $project_it;
		
		$sql = "SELECT p.* ".
			   "  FROM pm_Participant p " .
			   " WHERE EXISTS (SELECT 1 FROM pm_ParticipantRole r, pm_ProjectRole l" .
			   "				WHERE r.Participant = p.pm_ParticipantId " .
			   "                  AND r.ProjectRole = l.pm_ProjectRoleId" .
			   "				  AND l.ReferenceName = 'developer' ) " .
			   "   AND p.IsActive = 'Y' ".
			   "   AND p.Project = ".$project_it->getId();

		return $this->createSQLIterator($sql);
	}

	function getTesterIt()
	{
		global $project_it;
		
		$sql = "SELECT p.* ".
			   "  FROM pm_Participant p " .
			   " WHERE EXISTS (SELECT 1 FROM pm_ParticipantRole r, pm_ProjectRole l" .
			   "				WHERE r.Participant = p.pm_ParticipantId " .
			   "                  AND r.ProjectRole = l.pm_ProjectRoleId" .
			   "				  AND l.ReferenceName = 'tester') " .
			   "   AND p.IsActive = 'Y' ".
			   "   AND p.Project = ".$project_it->getId();

		return $this->createSQLIterator($sql);
	}

	function getLeadTeam( $project = 0 )
	{
		if ( $project < 1 ) $project = getSession()->getProjectIt()->getId();
		
		$sql = "SELECT p.* ".
			   "  FROM pm_Participant p " .
			   " WHERE EXISTS (SELECT 1 FROM pm_ParticipantRole r, pm_ProjectRole l" .
			   "				WHERE r.Participant = p.pm_ParticipantId " .
			   "                  AND r.ProjectRole = l.pm_ProjectRoleId" .
			   "				  AND l.ReferenceName = 'lead' ) " .
			   "   AND p.IsActive = 'Y' ".
			   "   AND p.Project = ".$project;

		return $this->createSQLIterator($sql);
	}

	function getTeam( $project = null )
	{
		global $project_it;
		
		if ( is_null($project) )
		{
			$project = $project_it->getId();
		}
		
		$sql = "SELECT p.* ".
			   "  FROM pm_Participant p " .
			   " WHERE p.IsActive = 'Y' ".
			   "   AND p.Project = ".$project;

		return $this->createSQLIterator($sql);
	}
	
	function getTeamWithCapacity()
	{
		$sql = "SELECT p.* ".
			   "  FROM pm_Participant p " .
			   " WHERE p.IsActive = 'Y'" .
			   "   AND (SELECT SUM(r.Capacity) FROM pm_ParticipantRole r " .
			   "	     WHERE r.Participant = p.pm_ParticipantId ) > 0 ".
			   $this->getVpdPredicate();

		return $this->createSQLIterator($sql);
	}

	function getTotallySubscribedIt()
	{
		global $project_it;
		
		$sql = "SELECT p.* ".
			   "  FROM pm_Participant p " .
			   " WHERE EXISTS (SELECT 1 FROM cms_UserSettings s " .
			   "				WHERE s.User = p.pm_ParticipantId " .
			   "                  AND s.Settings = 'SubscribeAllNotifications'" .
			   " 				  AND s.Value = 'Y' ) " .
			   "   AND p.IsActive = 'Y' ".
			   "   AND p.Project = ".(is_object($project_it) && $project_it->count() > 0 ? $project_it->getId() : "0");

		return $this->createSQLIterator($sql);
	}

	function hasTeamMembers( $role_it )
	{
		global $project_it;
		
		$sql = "SELECT COUNT(1) items ".
			   "  FROM pm_Participant p " .
			   " WHERE EXISTS (SELECT 1 FROM pm_ParticipantRole r, pm_ProjectRole l" .
			   "				WHERE r.Participant = p.pm_ParticipantId " .
			   "                  AND r.ProjectRole = ".$role_it->getId().") " .
			   "   AND p.IsActive = 'Y' ".
			   "   AND p.Project = ".$project_it->getId();

		$it = $this->createSQLIterator($sql);
		return $it->get('items') > 0;
	}

	function getDefaultAttributeValue( $attr )
	{
		global $project_it;
		
		switch ( $attr )
		{
			case 'Project':
				return $project_it->getId();
				
			case 'Capacity':
			    return 0;
			    
			default:
				return parent::getDefaultAttributeValue( $attr );
		}
	}
}
