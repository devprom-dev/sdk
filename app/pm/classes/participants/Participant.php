<?php

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

		$this->setAttributeCaption('IsActive', text(2178));
		$this->setAttributeDescription('IsActive', text(1054));

		$this->addAttribute('ParticipantRole', 'REF_ParticipantRoleId', translate('Роль в проекте'), false, false, '', 100);
		$this->addAttribute('ProjectRole', 'REF_ProjectRoleId', translate('Роль'), false, false, '', 101);
		$this->setAttributeRequired('ProjectRole', true);
		$this->addPersister( new ParticipantRolesPersister() );
		
		$this->setAttributeCaption('Capacity', translate('Ежедневная загрузка, ч.'));
		$this->setAttributeOrderNum('Capacity', 102);
		$this->setAttributeRequired('Capacity', true);
		$this->setAttributeDescription('NotificationEmailType', text(1913));
		$this->addPersister( new ParticipantDetailsPersister() );

        $this->setAttributeDefault('NotificationTrackingType', 'personal');
        $this->setAttributeDefault('NotificationEmailType', 'direct');

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
			$parms['Caption'] = $user_it->getHtmlDecoded('Caption');
			$parms['Login'] = $user_it->getHtmlDecoded('Login');
			$parms['Email'] = $user_it->getHtmlDecoded('Email');
			$parms['HomePhone'] = $user_it->getHtmlDecoded('Phone');
			$parms['ICQNumber'] = $user_it->getHtmlDecoded('ICQ');
			$parms['Skype'] = $user_it->getHtmlDecoded('Skype');
		}

		return parent::add_parms( $parms );
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
		
		return $session->getApplicationUrl().'module/permissions/participants?';
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
	
	function getLeadTeam( $project = 0 )
	{
		if ( $project < 1 ) return $this->getEmptyIterator();
		
		$sql = "SELECT p.* ".
			   "  FROM pm_Participant p " .
			   " WHERE EXISTS (SELECT 1 FROM pm_ParticipantRole r, pm_ProjectRole l" .
			   "				WHERE r.Participant = p.pm_ParticipantId " .
			   "                  AND r.ProjectRole = l.pm_ProjectRoleId" .
			   "				  AND l.ReferenceName = 'lead' ) " .
			   "   AND p.Project = ".$project;

		return $this->createSQLIterator($sql);
	}

	function hasTeamMembers( $role_it )
	{
		$sql = "SELECT COUNT(1) items ".
			   "  FROM pm_Participant p " .
			   " WHERE EXISTS (SELECT 1 FROM pm_ParticipantRole r, pm_ProjectRole l" .
			   "				WHERE r.Participant = p.pm_ParticipantId " .
			   "                  AND r.ProjectRole = ".$role_it->getId().") " .
			   "   AND p.Project = ".getSession()->getProjectIt()->getId();

		$it = $this->createSQLIterator($sql);
		return $it->get('items') > 0;
	}

	function getDefaultAttributeValue( $attr )
	{
		switch ( $attr )
		{
			case 'Project':
				return getSession()->getProjectIt()->getId();
			case 'Capacity':
			    return 0;
			default:
				return parent::getDefaultAttributeValue( $attr );
		}
	}
}
