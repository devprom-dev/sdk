<?php

include "HumanResourceIterator.php";
include "predicates/ResourcePredicate.php";
include "predicates/ResourceRolePredicate.php";
include "predicates/ResourceUsageProjectPredicate.php";
include "predicates/ResourceUsageUserPredicate.php";
include "predicates/ResourceUserPredicate.php";

class HumanResource extends Metaobject
{
 	function __construct() 
 	{
		parent::Metaobject('cms_User');
		
		$this->addAttribute( 'GroupId', 'INTEGER', '', false, true );
		$this->addAttribute( 'ResourceId', 'INTEGER', '', false, true );
		$this->addAttribute( 'ResourceClass', 'TEXT', '', false, true );
 	}

	function createIterator() 
	{
		return new HumanResourceIterator( $this );
	}
	
	function getAttributes()
	{
		$attrs = parent::getAttributes();
		
		foreach( $attrs as $key => $value ) {
			if ( !in_array( $key, array('ResourceId', 'ResourceClass', 'ResourceTitle', 'GroupId') ) ) {
				if ( strpos($key, 'Period') === false )
					unset($attrs[$key]);
			}
		}
		
		return $attrs;		
	}
	
	function getAll( $row_class, $row_ids, $divider_class = '', $divider_ids = array() )
	{
		$sql = '';
		
		if ( count($row_ids) < 1 ) $row_ids = array(0);
		
		if ( count($divider_ids) < 1 ) $divider_ids = array(0);

		switch ( $row_class )
		{
			case 'projectrolebase':
				switch ( $divider_class )
				{
					case 'user':
					case 'eeuser':
						$sql = " SELECT t.cms_UserId GroupId, t.cms_UserId ResourceId, ".
							   "		'Group' ResourceClass, t.Caption ResourceTitle ".
							   "   FROM cms_User t ".
							   "  WHERE EXISTS (SELECT 1 FROM pm_Participant p, pm_ParticipantRole pr, pm_ProjectRole prr ".
							   "				 WHERE pr.Participant = p.pm_ParticipantId ".
							   "				   AND pr.ProjectRole = prr.pm_ProjectRoleId ".
							   "				   AND prr.ProjectRoleBase IN (".join(',',$row_ids).") ".
							   "				   AND p.SystemUser = t.cms_UserId ) ".
							   "    AND t.cms_UserId IN (".join(',',$divider_ids).") ".
							   "  UNION ".
							   " SELECT t.cms_UserId GroupId, t.cms_UserId ResourceId, ".
							   "		'Group' ResourceClass, t.Caption ResourceTitle ".
							   "   FROM cms_User t ".
							   "  WHERE EXISTS (SELECT 1 FROM pm_Participant p, pm_Task s, pm_TaskType tt, pm_ProjectRole prr ".
							   "				 WHERE tt.pm_TaskTypeId = s.TaskType ".
							   "				   AND tt.ProjectRole = prr.pm_ProjectRoleId ".
							   "				   AND prr.ProjectRoleBase IN (".join(',',$row_ids).") ".
							   "				   AND p.SystemUser = s.Assignee ".
							   "				   AND p.SystemUser = t.cms_UserId ) ".
							   "    AND t.cms_UserId IN (".join(',',$divider_ids).") ".
							   "  UNION ".
							   " SELECT t.cms_UserId GroupId, t2.pm_ProjectRoleId ResourceId, ".
							   "		'Row' ResourceClass, t2.Caption ResourceTitle ".
							   "   FROM cms_User t, pm_ProjectRole t2 ".
							   "  WHERE t2.pm_ProjectRoleId IN (".join(',',$row_ids).")".
							   "    AND t2.ProjectRoleBase IS NULL ".
							   "    AND t.cms_UserId IN (".join(',',$divider_ids).") ".
							   "    AND EXISTS (SELECT 1 FROM pm_Participant p, pm_ParticipantRole pr, pm_ProjectRole prr ".
							   "				 WHERE pr.Participant = p.pm_ParticipantId ".
							   "				   AND pr.ProjectRole = prr.pm_ProjectRoleId ".
							   "				   AND p.SystemUser = t.cms_UserId ".
							   "				   AND prr.ProjectRoleBase = t2.pm_ProjectRoleId ) ".
							   "  UNION ".
							   " SELECT t.cms_UserId GroupId, t2.pm_ProjectRoleId ResourceId, ".
							   "		'Row' ResourceClass, t2.Caption ResourceTitle ".
							   "   FROM cms_User t, pm_ProjectRole t2 ".
							   "  WHERE t2.pm_ProjectRoleId IN (".join(',',$row_ids).")".
							   "    AND t2.ProjectRoleBase IS NULL ".
							   "    AND t.cms_UserId IN (".join(',',$divider_ids).") ".
							   "    AND EXISTS (SELECT 1 FROM pm_Participant p, pm_Task s, pm_TaskType tt, pm_ProjectRole prr ".
							   "				 WHERE s.Assignee = p.SystemUser ".
							   "				   AND p.SystemUser = t.cms_UserId ".
							   "				   AND tt.pm_TaskTypeId = s.TaskType ".
							   "				   AND tt.ProjectRole = prr.pm_ProjectRoleId ".
							   "				   AND prr.ProjectRoleBase = t2.pm_ProjectRoleId ) ".
							   "  ORDER BY 1, 3, 4 ";
						break;
					
					case 'project':
					case 'eeproject':
						$sql = " SELECT t.pm_ProjectId GroupId, t.pm_ProjectId ResourceId, ".
							   "		'Group' ResourceClass, t.Caption ResourceTitle ".
							   "   FROM pm_Project t ".
							   "  WHERE EXISTS (SELECT 1 FROM pm_Participant p, pm_ParticipantRole pr, pm_ProjectRole prr ".
							   "				 WHERE pr.Participant = p.pm_ParticipantId ".
							   "				   AND pr.ProjectRole = prr.pm_ProjectRoleId ".
							   "				   AND prr.ProjectRoleBase IN (".join(',',$row_ids).") ".
							   "				   AND p.Project = t.pm_ProjectId ) ".
							   "    AND t.pm_ProjectId IN (".join(',',$divider_ids).") ".
							   "  UNION ".
							   " SELECT t.pm_ProjectId GroupId, t.pm_ProjectId ResourceId, ".
							   "		'Group' ResourceClass, t.Caption ResourceTitle ".
							   "   FROM pm_Project t ".
							   "  WHERE EXISTS (SELECT 1 FROM pm_Task s, pm_TaskType tt, pm_ProjectRole prr ".
							   "				 WHERE s.VPD = t.VPD ".
							   "				   AND tt.pm_TaskTypeId = s.TaskType ".
							   "				   AND tt.ProjectRole = prr.pm_ProjectRoleId ".
							   "				   AND prr.ProjectRoleBase IN (".join(',',$row_ids).") ".
							   "			    ) ".
							   "    AND t.pm_ProjectId IN (".join(',',$divider_ids).") ".
							   "  UNION ".
							   " SELECT t.pm_ProjectId GroupId, t2.pm_ProjectRoleId ResourceId, ".
							   "		'Row' ResourceClass, t2.Caption ResourceTitle ".
							   "   FROM pm_Project t, pm_ProjectRole t2  ".
							   "  WHERE t.pm_ProjectId IN (".join(',',$divider_ids).")".
							   "    AND EXISTS (SELECT 1 FROM pm_Participant p, pm_ParticipantRole pr, pm_ProjectRole prr ".
							   "				 WHERE pr.Participant = p.pm_ParticipantId ".
							   "				   AND pr.ProjectRole = prr.pm_ProjectRoleId ".
							   "				   AND p.Project = t.pm_ProjectId ".
							   "				   AND prr.ProjectRoleBase = t2.pm_ProjectRoleId) ".
							   "    AND t2.pm_ProjectRoleId IN (".join(',',$row_ids).") ".
							   "    AND t2.ProjectRoleBase IS NULL ".
							   "  UNION ".
							   " SELECT t.pm_ProjectId GroupId, t2.pm_ProjectRoleId ResourceId, ".
							   "		'Row' ResourceClass, t2.Caption ResourceTitle ".
							   "   FROM pm_Project t, pm_ProjectRole t2 ".
							   "  WHERE t.pm_ProjectId IN (".join(',',$divider_ids).")".
							   "    AND EXISTS (SELECT 1 FROM pm_Task s, pm_TaskType tt, pm_ProjectRole prr ".
							   "				 WHERE s.VPD = t.VPD ".
							   "				   AND tt.pm_TaskTypeId = s.TaskType ".
							   "				   AND tt.ProjectRole = prr.pm_ProjectRoleId ".
							   "				   AND prr.ProjectRoleBase = t2.pm_ProjectRoleId) ".
							   "    AND t2.pm_ProjectRoleId IN (".join(',',$row_ids).") ".
							   "    AND t2.ProjectRoleBase IS NULL ".
							   "  ORDER BY 1, 3, 4";
						break;
						
					default:
						$sql = " SELECT '' GroupId, t.pm_ProjectRoleId ResourceId, ".
							   "	    'Row' ResourceClass, t.Caption ResourceTitle ".
							   "   FROM pm_ProjectRole t ".
							   "  WHERE t.pm_ProjectRoleId IN (".join(',',$row_ids).") ORDER BY 4";
				}				
				break;
				
			case 'projectrole':
				$sql = " SELECT '' GroupId, t.pm_ProjectRoleId ResourceId, ".
					   "	    'Row' ResourceClass, t.Caption ResourceTitle ".
					   "   FROM pm_ProjectRole t ".
					   "  WHERE t.pm_ProjectRoleId IN (".join(',',$row_ids).") ORDER BY 4 ";
				break;
				
			case 'project':
			case 'eeproject':
				switch ( $divider_class )
				{
					case 'projectrolebase':
						$sql = " SELECT t.pm_ProjectRoleId GroupId, t.pm_ProjectRoleId ResourceId, ".
							   "		'Group' ResourceClass, t.Caption ResourceTitle ".
							   "   FROM pm_ProjectRole t ".
							   "  WHERE EXISTS (SELECT 1 FROM pm_Participant p, pm_ParticipantRole pr, pm_ProjectRole prr ".
							   "				 WHERE pr.Participant = p.pm_ParticipantId ".
							   "				   AND pr.ProjectRole = prr.pm_ProjectRoleId ".
							   "				   AND prr.ProjectRoleBase = t.pm_ProjectRoleId ".
							   "				   AND p.Project IN (".join(',',$row_ids).")) ".
							   "    AND t.pm_ProjectRoleId IN (".join(',',$divider_ids).") ".
							   "    AND t.ProjectRoleBase IS NULL ".
							   "  UNION ".
							   " SELECT t.pm_ProjectRoleId GroupId, t.pm_ProjectRoleId ResourceId, ".
							   "		'Group' ResourceClass, t.Caption ResourceTitle ".
							   "   FROM pm_ProjectRole t ".
							   "  WHERE EXISTS (SELECT 1 FROM pm_Project i, pm_Task s, pm_TaskType tt, pm_ProjectRole prr ".
							   "				 WHERE s.VPD = i.VPD ".
							   "				   AND tt.pm_TaskTypeId = s.TaskType ".
							   "				   AND tt.ProjectRole = prr.pm_ProjectRoleId ".
							   "				   AND prr.ProjectRoleBase = t.pm_ProjectRoleId ".
							   "				   AND i.pm_ProjectId IN (".join(',',$row_ids).")) ".
							   "    AND t.pm_ProjectRoleId IN (".join(',',$divider_ids).") ".
							   "    AND t.ProjectRoleBase IS NULL ".
							   "  UNION ".
							   " SELECT t2.pm_ProjectRoleId GroupId, t.pm_ProjectId ResourceId, ".
							   "		'Row' ResourceClass, t.Caption ResourceTitle ".
							   "   FROM pm_Project t, pm_ProjectRole t2  ".
							   "  WHERE t.pm_ProjectId IN (".join(',',$row_ids).")".
							   "    AND EXISTS (SELECT 1 FROM pm_Participant p, pm_ParticipantRole pr, pm_ProjectRole prr ".
							   "				 WHERE pr.Participant = p.pm_ParticipantId ".
							   "				   AND pr.ProjectRole = prr.pm_ProjectRoleId ".
							   "				   AND p.Project = t.pm_ProjectId ".
							   "				   AND prr.ProjectRoleBase = t2.pm_ProjectRoleId) ".
							   "    AND t2.pm_ProjectRoleId IN (".join(',',$divider_ids).") ".
							   "    AND t2.ProjectRoleBase IS NULL ".
							   "  UNION ".
							   " SELECT t2.pm_ProjectRoleId GroupId, t.pm_ProjectId ResourceId, ".
							   "		'Row' ResourceClass, t.Caption ResourceTitle ".
							   "   FROM pm_Project t, pm_ProjectRole t2 ".
							   "  WHERE t.pm_ProjectId IN (".join(',',$row_ids).")".
							   "    AND EXISTS (SELECT 1 FROM pm_Task s, pm_TaskType tt, pm_ProjectRole prr ".
							   "				 WHERE s.VPD = t.VPD ".
							   "				   AND tt.pm_TaskTypeId = s.TaskType ".
							   "				   AND tt.ProjectRole = prr.pm_ProjectRoleId ".
							   "				   AND prr.ProjectRoleBase = t2.pm_ProjectRoleId) ".
							   "    AND t2.pm_ProjectRoleId IN (".join(',',$divider_ids).") ".
							   "    AND t2.ProjectRoleBase IS NULL ".
							   "  ORDER BY 1, 3, 4 ";
						break;
						
					case 'user':
					case 'eeuser':
						$sql = " SELECT t.cms_UserId GroupId, t.cms_UserId ResourceId, ".
							   "		'Group' ResourceClass, t.Caption ResourceTitle ".
							   "   FROM cms_User t ".
							   "  WHERE EXISTS (SELECT 1 FROM pm_Participant p ".
							   "				 WHERE p.SystemUser = t.cms_UserId ".
							   "				   AND p.Project IN (".join(',',$row_ids).")) ".
							   "    AND t.cms_UserId IN (".join(',',$divider_ids).") ".
							   "  UNION ALL ".
							   " SELECT p.SystemUser GroupId, t.pm_ProjectId ResourceId, ".
							   "		'Row' ResourceClass, t.Caption ResourceTitle ".
							   "   FROM pm_Project t, pm_Participant p ".
							   "  WHERE t.pm_ProjectId IN (".join(',',$row_ids).")".
							   "    AND t.pm_ProjectId = p.Project ".
							   "    AND p.SystemUser IN (".join(',',$divider_ids).") ".
							   "  ORDER BY 1, 3, 4 ";
						break;
						
					default:
						$sql = " SELECT '' GroupId, t.pm_ProjectId ResourceId, ".
							   "		'Project' ResourceClass, t.Caption ResourceTitle ".
							   "   FROM pm_Project t ".
							   "  WHERE t.pm_ProjectId IN (".join(',',$row_ids).") ORDER BY 4";
				}
				break;				

			case 'user':
			case 'eeuser':
			default:
				switch ( $divider_class )
				{
					case 'projectrolebase':
						$sql = " SELECT t.pm_ProjectRoleId GroupId, t.pm_ProjectRoleId ResourceId, ".
							   "		'Group' ResourceClass, t.Caption ResourceTitle ".
							   "   FROM pm_ProjectRole t ".
							   "  WHERE EXISTS (SELECT 1 FROM pm_Participant p, pm_ParticipantRole pr, pm_ProjectRole prr ".
							   "				 WHERE pr.Participant = p.pm_ParticipantId ".
							   "				   AND pr.ProjectRole = prr.pm_ProjectRoleId ".
							   "				   AND prr.ProjectRoleBase = t.pm_ProjectRoleId ".
							   "				   AND p.SystemUser IN (".join(',',$row_ids).")) ".
							   "    AND t.pm_ProjectRoleId IN (".join(',',$divider_ids).") ".
							   "    AND t.ProjectRoleBase IS NULL ".
							   "  UNION ".
							   " SELECT t.pm_ProjectRoleId GroupId, t.pm_ProjectRoleId ResourceId, ".
							   "		'Group' ResourceClass, t.Caption ResourceTitle ".
							   "   FROM pm_ProjectRole t ".
							   "  WHERE EXISTS (SELECT 1 FROM pm_Participant p, pm_Task s, pm_TaskType tt, pm_ProjectRole prr ".
							   "				 WHERE tt.pm_TaskTypeId = s.TaskType ".
							   "				   AND tt.ProjectRole = prr.pm_ProjectRoleId ".
							   "				   AND prr.ProjectRoleBase = t.pm_ProjectRoleId ".
							   "				   AND p.SystemUser = s.Assignee ".
							   "				   AND p.SystemUser IN (".join(',',$row_ids).")) ".
							   "    AND t.pm_ProjectRoleId IN (".join(',',$divider_ids).") ".
							   "    AND t.ProjectRoleBase IS NULL ".
							   "  UNION ".
							   " SELECT t2.pm_ProjectRoleId GroupId, t.cms_UserId ResourceId, ".
							   "		'Row' ResourceClass, t.Caption ResourceTitle ".
							   "   FROM cms_User t, pm_ProjectRole t2 ".
							   "  WHERE t.cms_UserId IN (".join(',',$row_ids).")".
							   "    AND EXISTS (SELECT 1 FROM pm_Participant p, pm_ParticipantRole pr, pm_ProjectRole prr ".
							   "				 WHERE pr.Participant = p.pm_ParticipantId ".
							   "				   AND pr.ProjectRole = prr.pm_ProjectRoleId ".
							   "				   AND p.SystemUser = t.cms_UserId ".
							   "				   AND prr.ProjectRoleBase = t2.pm_ProjectRoleId) ".
							   "    AND t2.pm_ProjectRoleId IN (".join(',',$divider_ids).") ".
							   "    AND t2.ProjectRoleBase IS NULL ".
							   "  UNION ".
							   " SELECT t2.pm_ProjectRoleId GroupId, t.cms_UserId ResourceId, ".
							   "		'Row' ResourceClass, t.Caption ResourceTitle ".
							   "   FROM cms_User t, pm_ProjectRole t2 ".
							   "  WHERE t.cms_UserId IN (".join(',',$row_ids).")".
							   "    AND EXISTS (SELECT 1 FROM pm_Participant p, pm_Task s, pm_TaskType tt, pm_ProjectRole prr ".
							   "				 WHERE s.Assignee = p.SystemUser ".
							   "				   AND p.SystemUser = t.cms_UserId ".
							   "				   AND tt.pm_TaskTypeId = s.TaskType ".
							   "				   AND tt.ProjectRole = prr.pm_ProjectRoleId ".
							   "				   AND prr.ProjectRoleBase = t2.pm_ProjectRoleId) ".
							   "    AND t2.pm_ProjectRoleId IN (".join(',',$divider_ids).") ".
							   "    AND t2.ProjectRoleBase IS NULL ".
							   "  ORDER BY 1, 3, 4 ";
						break;
					
					case 'project':
					case 'eeproject':
						$sql = " SELECT t.pm_ProjectId GroupId, t.pm_ProjectId ResourceId, ".
							   "		'Group' ResourceClass, t.Caption ResourceTitle ".
							   "   FROM pm_Project t ".
							   "  WHERE EXISTS (SELECT 1 FROM pm_Participant p ".
							   "				 WHERE p.Project = t.pm_ProjectId ".
							   "				   AND p.SystemUser IN (".join(',',$row_ids).")) ".
							   "    AND t.pm_ProjectId IN (".join(',',$divider_ids).") ".
							   "  UNION ALL ".
							   " SELECT p.Project GroupId, t.cms_UserId ResourceId, ".
							   "		'Row' ResourceClass, t.Caption ResourceTitle ".
							   "   FROM cms_User t, pm_Participant p ".
							   "  WHERE t.cms_UserId IN (".join(',',$row_ids).")".
							   "    AND t.cms_UserId = p.SystemUser ".
							   "    AND p.Project IN (".join(',',$divider_ids).") ".
							   "  ORDER BY 1, 3, 4 ";
						break;
						
					default:
						$sql = " SELECT '' GroupId, t.cms_UserId ResourceId, ".
							   "		'User' ResourceClass, t.Caption ResourceTitle ".
							   "   FROM cms_User t ".
							   "  WHERE t.cms_UserId IN (".join(',',$row_ids).") ORDER BY 4";
				}
				break;				
		}

		return $this->createSQLIterator($sql);
	}
	
	function getUsageByInterval( $interval, $year, $month, $row_class, $divider = '' )
	{
		if ( $interval == '' || $interval == 'all' )
		{
			$interval = 'quarter';
		}
		else
		{
			$interval = mysql_real_escape_string($interval);
		}
		
		if ( $year == '' || $year == 'all' )
		{
			$year = date('Y');
		}
		else
		{
			$year = mysql_real_escape_string($year);
		}
		
		if ( $month == 'all' )
		{
			$month = '';
		}

		$this->_cacheReleaseDates( $year );

		$this->_cacheWorkload( $year );

		$month = mysql_real_escape_string($month);

		switch ( $interval )
		{
			case 'quarter':
				$intervalIdField = "IntervalQuarter";
				break;
				
			case 'month':
				$intervalIdField = "IntervalMonth";
				break;

			case 'week':
				$intervalIdField = "IntervalWeek";
				break;
		}
		
		switch ( $row_class )
		{
			case 'projectrolebase':
				$row_id = " p.ProjectRoleBase ";
				break;
				
			case 'projectrole':
				$row_id = " p.ProjectRole ";
				break;

			case 'project':
			case 'eeproject':
				$row_id = " p.Project ";
				break;				

			case 'user':
			case 'eeuser':
			default:
				$row_id = " p.SystemUser ";
				break;				
		}

		switch ( $divider )
		{
			case 'projectrolebase':
				$divider_row_id = " p.ProjectRoleBase GroupId, ";
				break;
				
			case 'projectrole':
				$divider_row_id = " p.ProjectRole GroupId, ";
				break;

			case 'project':
			case 'eeproject':
				$divider_row_id = " p.Project GroupId, ";
				break;				

			case 'user':
			case 'eeuser':
				$divider_row_id = " p.SystemUser GroupId, ";
				break;
			
			default:
				$divider_row_id = "";
				break;				
		}
		
		$interval_predicate = "AND i.IntervalYear = ".$year.
			($month != '' ? " AND i.IntervalMonth = ".$month : "");
		
		$sql = " SELECT ".$divider_row_id.$row_id." RowId, i.Caption IntervalCaption," .
			   "        FLOOR((TO_DAYS(i.FinishDate) - TO_DAYS(i.StartDate) + 1) / 7 * t.DaysInWeek) * COUNT(DISTINCT p.SystemUser) * 8 MaxUsageDuration," .
			   "		SUM(t.Duration * p.Capacity) UsageDuration, ".
			   "		0 WorkloadDuration, 0 WorkloadActual, '' Tasks " .$group_id.
			   "   FROM pm_CalendarInterval i," .
			   "	    (SELECT p.Project, par.ProjectRole, prr.ProjectRoleBase, p.SystemUser, par.Capacity Capacity " .
			   "  		   FROM pm_Participant p, " .
			   "       		    pm_ParticipantRole par, " .
			   "				pm_ProjectRole prr " .
			   " 	      WHERE p.IsActive = 'Y' " .$this->getFilterPredicate().
			   "   		    AND par.Participant = p.pm_ParticipantId" .
			   "			AND prr.pm_ProjectRoleId = par.ProjectRole" .
			   "            AND par.Capacity > 0 ".
			   "		) p," .
			   "	    (SELECT v.Project, v.DaysInWeek, i.".$intervalIdField." IntervalId, COUNT(1) Duration" .
			   "           FROM pm_CalendarInterval i," .
			   "			    tmp_ReleaseDate v " .
			   "		  WHERE i.StartDateOnly BETWEEN v.StartDate AND v.FinishDate" .
			   "		    AND i.MinDaysInWeek <= v.DaysInWeek" .
			   "		    AND i.Kind = 'day' " .$interval_predicate.
			   "          GROUP BY 1, 2, 3" .
			   "		) t " .
			   "  WHERE i.Kind = '".$interval."'" .
			   "    AND i.".$intervalIdField." = t.IntervalId " .
			   "    AND p.Project = t.Project ".$interval_predicate.
			   "  GROUP BY 1, 2 ".($divider_row_id != '' ? ', 3' : '').
			   "  UNION ".
			   " SELECT ".$divider_row_id.$row_id.", i.Caption IntervalCaption, " .
			   "        FLOOR((TO_DAYS(i.FinishDate) - TO_DAYS(i.StartDate) + 1) / 7 * p.DaysInWeek) * COUNT(DISTINCT p.SystemUser) * 8," .
			   "		0, SUM( p.Duration ) WorkloadDuration, SUM( p.Actual ) WorkloadActual, GROUP_CONCAT(DISTINCT p.Tasks) Tasks " .$group_id.
			   "   FROM pm_CalendarInterval i," .
			   "	    (SELECT i.".$intervalIdField." IntervalId, t.Project, " .
			   "				t.ProjectRole, t.ProjectRoleBase, t.SystemUser, t.DaysInWeek, " .
			   "				SUM(t.Workload) Duration, SUM(t.Actual) Actual, GROUP_CONCAT(DISTINCT t.Tasks) Tasks " .
			   "           FROM pm_CalendarInterval i," .	
			   "				tmp_WorkloadCalendar t " .
			   "		  WHERE i.Kind = 'day' ".
			   "			AND i.StartDateOnly = t.StartDate " .$interval_predicate.
			   "          GROUP BY 1, 2, 3, 4, 5, 6" .
			   "		) p " .
			   "  WHERE i.Kind = '".$interval."'" .
			   "    AND i.".$intervalIdField." = p.IntervalId " .$interval_predicate.
			   "  GROUP BY 1, 2".($divider_row_id != '' ? ', 3' : '').
			   "  ORDER BY ".($divider_row_id != "" ? "1, 2, 3, 5" : " 1, 2, 5");

		return $this->createSQLIterator( $sql );
	}
	
	function _cacheReleaseDates( $year )
	{
		$sql = " CREATE TEMPORARY TABLE tmp_ReleaseDate (" .
			   "	Project INTEGER, DaysInWeek INTEGER, StartDate DATE, FinishDate DATE ) ";
		
		DAL::Instance()->Query($sql);
		
		$sql = " INSERT INTO tmp_ReleaseDate" .
			   " SELECT pr.pm_ProjectId, pr.DaysInWeek, " .
			   "	 	DATE(".
			   "			IFNULL( v.StartDate," .
			   "				(SELECT MAX(m.MetricValueDate) " .
			   "				   FROM pm_VersionMetric m " .
			   "			      WHERE m.Version = v.pm_VersionId " .
			   "					AND m.Metric = 'EstimatedStart' )) ) StartDate, " .
			   "	 	DATE(".
			   "		 	IFNULL( v.FinishDate," .
			   "				(SELECT MAX(m.MetricValueDate) " .
			   "				   FROM pm_VersionMetric m " .
			   "			      WHERE m.Version = v.pm_VersionId " .
			   "					AND m.Metric = 'EstimatedFinish' )) ) FinishDate " .
			   "   FROM pm_Version v, pm_Project pr, pm_Methodology me " .
			   "  WHERE pr.pm_ProjectId = v.Project ".
			   "	AND me.Project = v.Project ".
			   "	AND me.IsPlanningUsed <> 'N' ".			   
		       "    AND EXISTS (SELECT 1 " .
			   "				  FROM pm_Participant p, pm_ProjectRole prr, " .
			   "					   pm_ParticipantRole par" .
			   "			     WHERE p.Project = v.Project" .
			   "			       AND par.Participant = p.pm_ParticipantId" .
 			   "			       AND prr.pm_ProjectRoleId = par.ProjectRole " .
 			   					   $this->getFilterPredicate().
			   "			    ) ".
			   "  UNION ALL ".
			   " SELECT pr.pm_ProjectId, pr.DaysInWeek, " .
			   "	 	DATE(pr.RecordCreated) StartDate, " .
			   "	 	IF(pr.IsClosed = 'N', LAST_DAY(DATE_ADD(NOW(), INTERVAL 12 MONTH)), DATE(pr.RecordModified)) FinishDate " .
			   "   FROM pm_Project pr, pm_Methodology me " .
			   "  WHERE me.Project = pr.pm_ProjectId ".
			   "	AND me.IsPlanningUsed = 'N' ".			   
		       "    AND EXISTS (SELECT 1 " .
			   "				  FROM pm_Participant p, pm_ProjectRole prr, " .
			   "					   pm_ParticipantRole par" .
			   "			     WHERE p.Project = pr.pm_ProjectId" .
			   "			       AND par.Participant = p.pm_ParticipantId" .
 			   "			       AND prr.pm_ProjectRoleId = par.ProjectRole " .
 			   					   $this->getFilterPredicate().
			   "			    ) ";
		
		DAL::Instance()->Query($sql);
	}

	function _cacheWorkload( $year )
	{
		$sql = " CREATE TEMPORARY TABLE tmp_WorkloadDate (" .
			   "	Project INTEGER, DaysInWeek INTEGER, ProjectRole INTEGER, ProjectRoleBase INTEGER, SystemUser INTEGER, " .
			   "	StartDate DATE, FinishDate DATE, DailyCapacity INTEGER, Duration INTEGER, Tasks TEXT ) ";
		
		DAL::Instance()->Query($sql);

		$sql = " INSERT INTO tmp_WorkloadDate " .
			   " SELECT t.Project, pro.DaysInWeek, t.ProjectRole, prr.ProjectRoleBase, p.SystemUser, " .
			   "        t.StartDate, t.FinishDate, ". 
			   "		(SELECT ROUND(SUM(r.Capacity)) FROM pm_ParticipantRole r, pm_Participant pt " .
			   "		  WHERE r.Participant = pt.pm_ParticipantId AND pt.SystemUser = t.Assignee AND pt.Project = t.Project) DailyCapacity, " .
			   "	    t.Planned Duration, t.pm_TaskId " .
			   "   FROM (" .
			   "		SELECT p.pm_ProjectId Project, ".
			   "			   IFNULL(tt.ProjectRole, (SELECT MAX(p.ProjectRole) FROM pm_ParticipantRole p, pm_Participant pt WHERE p.Participant = pt.pm_ParticipantId AND pt.SystemUser = t.Assignee AND pt.Project = p.pm_ProjectId)) ProjectRole, ".
			   "			   t.Assignee, " .
			   "			   t.pm_TaskId, t.Planned, t.State, t.StartDate, t.FinishDate " .
			   "		  FROM pm_Project p," .	
			   "			   pm_Task t," .
			   "	    	   pm_TaskType tt " .
			   "  		 WHERE t.TaskType = tt.pm_TaskTypeId ".
			   "    	   AND t.VPD = p.VPD " .
			   "		) t LEFT JOIN pm_ProjectRole prr ON prr.pm_ProjectRoleId = t.ProjectRole," .
			   "		pm_Participant p," .
			   "		pm_Project pro ".
			   "  WHERE pro.pm_ProjectId = t.Project ".
			   "	AND p.Project = t.Project ".
			   "	AND p.SystemUser = t.Assignee".$this->getFilterPredicate();

		DAL::Instance()->Query($sql);

		$sql = " CREATE TEMPORARY TABLE tmp_WorkloadCalendar (" .
			   "	DUMMY1 INTEGER, DUMMY2 INTEGER, Project INTEGER, DaysInWeek INTEGER, ProjectRole INTEGER, ProjectRoleBase INTEGER, SystemUser INTEGER, " .
			   "	StartDate DATE, IntervalYear INTEGER, IntervalMonth INTEGER, " .
			   "	IntervalQuarter INTEGER, IntervalWeek INTEGER, Workload INTEGER, Actual INTEGER, Tasks TEXT ) ";
		
		DAL::Instance()->Query($sql);

		$sql = " INSERT INTO tmp_WorkloadCalendar" .
			   " SELECT IF(@task_id=w.Tasks,(SELECT @row_num:=@row_num+1),(SELECT @row_num:=0)), ".
			   "		IF(@task_id=w.Tasks,(SELECT @task_id),(SELECT @task_id:=w.Tasks)), ".
			   "		w.Project, w.DaysInWeek, w.ProjectRole, w.ProjectRoleBase, w.SystemUser, " .
			   "		w.StartDate, w.IntervalYear, w.IntervalMonth, w.IntervalQuarter, w.IntervalWeek," .
			   "		LEAST(w.DailyCapacity,w.Duration-w.DailyCapacity*(SELECT @row_num)), 0, w.Tasks " .
			   "   FROM (SELECT w.Project, w.DaysInWeek, w.ProjectRole, w.ProjectRoleBase, w.SystemUser, " .
			   "				c.StartDate, c.IntervalYear, c.IntervalMonth, c.IntervalQuarter, c.IntervalWeek," .
			   "				w.Duration, w.DailyCapacity, w.Tasks " .
			   "   		   FROM tmp_WorkloadDate w, " .
			   "				pm_CalendarInterval c, " .
			   "				(SELECT @row_num:=0,@task_id:=0) rn ".
			   "  		  WHERE c.StartDateOnly BETWEEN w.StartDate AND DATE_ADD(w.StartDate, INTERVAL ABS(FLOOR(-w.Duration / w.DailyCapacity)) - 1 DAY) " .
			   "    		AND c.kind = 'day' " .
			   "    		AND c.IntervalYear = ".$year.
			   "		  ORDER BY w.Tasks) w";

		DAL::Instance()->Query($sql);
		
		$sql = " INSERT INTO tmp_WorkloadCalendar" .
			   " SELECT 0, 0, p.Project, pro.DaysInWeek, ".
			   "		IFNULL(tt.ProjectRole, (SELECT MAX(p.ProjectRole) FROM pm_ParticipantRole p, pm_Participant pt WHERE p.Participant = pt.pm_ParticipantId AND pt.SystemUser = t.Assignee AND pt.Project = p.Project)), ".
			   "		IFNULL(prr.ProjectRoleBase, (SELECT MAX(pr.ProjectRoleBase) FROM pm_ParticipantRole p, pm_ProjectRole pr, pm_Participant pt WHERE p.Participant = pt.pm_ParticipantId AND pt.SystemUser = t.Assignee AND pt.Project = p.Project AND pr.pm_ProjectRoleId = p.ProjectRole)), ".
			   "		p.SystemUser, " .
			   "	 	a.ReportDate, YEAR(a.ReportDate), MONTH(a.ReportDate), QUARTER(a.ReportDate), WEEK(a.ReportDate), " .
			   "	    IF(t.Planned IS NULL, a.Capacity, 0), a.Capacity, t.pm_TaskId " .
			   "   FROM pm_Task t," .
			   "		pm_Activity a, " .
			   "		pm_Participant p," .
			   "	    pm_TaskType tt LEFT JOIN pm_ProjectRole prr ON tt.ProjectRole = prr.pm_ProjectRoleId, " .
		 	   " 		pm_Project pro ".
			   "  WHERE pro.pm_ProjectId = p.Project ".
			   "	AND t.TaskType = tt.pm_TaskTypeId ".
			   "    AND a.Task = t.pm_TaskId ".
			   "    AND p.pm_ParticipantId = a.Participant ".$this->getFilterPredicate();

		DAL::Instance()->Query($sql);
	}
}