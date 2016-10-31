<?php

include "ProjectIterator.php";
include "predicates/ProjectCurrentPredicate.php";
include "predicates/ProjectExceptCurrentPredicate.php";
include "predicates/ProjectFilterPredicate.php";
include "predicates/ProjectLinkedPredicate.php";
include "predicates/ProjectParticipatePredicate.php";
include "predicates/ProjectRolePredicate.php";
include "predicates/ProjectStatePredicate.php";
include "predicates/ProjectUserLeadPredicate.php";
include "predicates/ProjectVpdPredicate.php";
include "predicates/ProjectAccessiblePredicate.php";
include "persisters/ProjectVPDPersister.php";
include "persisters/ProjectLeadsPersister.php";
include "persisters/ProjectLinkedPersister.php";
include "validators/ModelValidatorProjectCodeName.php";
include "sorts/SortProjectImportanceClause.php";
include "sorts/SortImportanceClause.php";
include "sorts/SortProjectSelfFirstClause.php";

class Project extends Metaobject 
{
 	var $metrics_it;
 	
 	function __construct( ObjectRegistrySQL $registry = null ) 
 	{
		parent::__construct('pm_Project', $registry);
        $this->setAttributeType( 'CodeName', 'VARCHAR' );
        $this->addAttribute( 'LinkedProject', 'REF_pm_ProjectId', translate('Связанные проекты'), false );
        $this->addPersister( new ProjectLinkedPersister() );
		$this->setSortDefault(
			array (
				new SortImportanceClause('Importance'),
				new SortAttributeClause('Caption')
			)
		);
 	}
	
 	function createIterator() 
	{
		return new ProjectIterator( $this );
	}
	
	function IsDeletedCascade( $object )
	{
	    switch ( $object->getEntityRefName() )
	    {
	        case 'pm_ProjectLink':
	        case 'pm_Methodology':
	            return true;
	            
	        default:
	            return false;
	    }
	}
	
	function getNotUsedProjectIt()
	{
		$sql = "select p.*," .
			   "	   to_days(now()) - to_days(max(u.RecordModified)) DaysNotUsed" .
			   "  from pm_Project p inner join pm_ProjectUse u on u.Project = p.pm_ProjectId" .
			   " where IFNULL(p.IsClosed, 'N') = 'N'" .
			   " group by p.pm_ProjectId ";
			   
		return $this->createSqlIterator($sql);
	}
	
	function getActiveIt( $days )
	{
		$sql = "select p.*, max(u.RecordModified) LastAccessed " .
			   "  from pm_Project p inner join pm_ProjectUse u on u.Project = p.pm_ProjectId" .
			   " where IFNULL(p.IsClosed, 'N') = 'N'" .
			   "   and to_days(now()) - to_days(u.RecordModified) < ".$days.
			   " group by p.pm_ProjectId " .
			   " order by LastAccessed DESC ";

		return $this->createSqlIterator($sql);
	}

	function getNotEmptyIt( $days )
	{
		$sql = "select p.* " .
			   "  from pm_Project p " .
			   " where IFNULL(p.IsClosed, 'N') = 'N'" .
			   "   and (SELECT COUNT(1) FROM pm_ChangeRequest r WHERE r.Project = p.pm_ProjectId) > 1 " .
			   " order by (SELECT COUNT(1) FROM pm_ChangeRequest r WHERE r.Project = p.pm_ProjectId) DESC ";
			   
		return $this->createSqlIterator($sql);
	}

	function getAttributeUserName( $attr )
	{
		switch ( $attr )
		{
			default:
				return parent::getAttributeUserName( $attr );
		}
	}
	
	function getProjectItToBeExported( $hash )
	{
		$it = $this->getAll();
		for( $i = 0; $i < $it->count(); $i++ )
		{
			if ( md5($it->getId().PRJ_EXPORT_KEY) == $hash ) {
				return $this->getExact($it->getId());
			}
			$it->moveNext();
		}
		
		$this->getExact(0);
	}
	
	function getActiveProjects( $user_id )
	{
		$sql = 
			" SELECT p.*, MAX(u.RecordModified) LastAccessed " .
			"   FROM pm_Project p " .
			"		 INNER JOIN pm_Participant r ON p.pm_ProjectId = r.Project " .
			"        LEFT OUTER JOIN pm_ProjectUse u ON u.Participant = r.SystemUser AND u.Project = p.pm_ProjectId " .
			"  WHERE r.SystemUser = ".$user_id.
			"    AND IFNULL(p.IsClosed, 'N') = 'N' ".
			"  GROUP BY p.pm_ProjectId ORDER BY LastAccessed DESC";

		return $this->createSQLIterator( $sql );
	}

	function getUserRelatedProjects( $user_id )
	{
		$sql = 
			" SELECT p.*, MAX(u.RecordModified) LastAccessed " .
			"   FROM pm_Project p " .
			"        LEFT OUTER JOIN pm_ProjectUse u ON u.Participant = ".$user_id." AND u.Project = p.pm_ProjectId " .
			"  WHERE EXISTS (SELECT 1 FROM pm_Participant r " .
			"				  WHERE p.pm_ProjectId = r.Project" .
			"					AND IFNULL(p.IsClosed, 'N') = 'N' ".
			"					AND r.SystemUser = ".$user_id.
			" 				  UNION " .
			"				 SELECT 1 FROM co_ProjectSubscription s " .
			"				  WHERE s.Project = p.pm_ProjectId" .
			"				    AND s.SystemUser = ".$user_id.") ".
			"  GROUP BY p.pm_ProjectId ORDER BY LastAccessed DESC";

		return $this->createSQLIterator( $sql );
	}

	function getActualProjects()
	{
		$user_it = getSession()->getUserIt();
		
		$sql = 
			" SELECT p.*, (SELECT MAX(u.RecordModified) " .
			"			     FROM pm_Participant r LEFT OUTER JOIN pm_ProjectUse u " .
			"						ON u.Participant = r.SystemUser AND u.Project = r.Project" .
			"		        WHERE p.pm_ProjectId = r.Project" .
			"				  AND r.SystemUser = ".$user_it->getId().
			"    		   ) LastAccessed " .
			"   FROM pm_Project p " .
			"  WHERE IFNULL(p.IsClosed, 'N') = 'N'" .
			"    AND EXISTS (SELECT 1" .
			"			       FROM pm_PublicInfo i ".
			"				  WHERE i.Project = p.pm_ProjectId" .
			"    			    AND i.IsProjectInfo = 'Y'" .
			"				  UNION ALL" .
			"				 SELECT 1" .
			"			       FROM pm_Participant t ".
			"				  WHERE t.Project = p.pm_ProjectId" .
			"				    AND t.SystemUser = ".$user_it->getId()." )".
			"  ORDER BY LastAccessed DESC ";

		return $this->createSQLIterator( $sql );
	}

	function getTeamProjects( $user_array )
	{
		$sql = 
			" SELECT p.* " .
			"   FROM pm_Project p " .
			"  WHERE (SELECT COUNT(1) FROM pm_Participant r ". 
			"		   WHERE p.pm_ProjectId = r.Project " .
			"  			 AND r.SystemUser IN (".join(',', $user_array).")" .
			"    	  ) = ".count($user_array);

		return $this->createSQLIterator( $sql );
	}

	function getPublicIt( $code_name )
	{
		$sql = 
			" SELECT p.* " .
			"   FROM pm_Project p, pm_PublicInfo i " .
			"  WHERE i.Project = p.pm_ProjectId" .
			"    AND p.CodeName = '".trim(strtolower($code_name))."'".
			"    AND IFNULL(i.IsProjectInfo, 'N') = 'Y' " .
			"  ORDER BY p.RecordCreated DESC ";

		return $this->createSQLIterator( $sql );
	}

	function getLatestPublicIt( $limit = 10 )
	{
		$sql = 
			" SELECT p.* " .
			"   FROM pm_Project p, pm_PublicInfo i " .
			"  WHERE i.Project = p.pm_ProjectId" .
			"    AND IFNULL(i.IsProjectInfo, 'N') = 'Y' " .
			"    AND IFNULL(p.IsClosed, 'N') = 'N'" .
			"  ORDER BY p.RecordCreated DESC ".
			"  LIMIT ".$limit;

		return $this->createSQLIterator( $sql );
	}

	function getLatestTopRatedPublicIt( $limit = 10 )
	{
		$sql = 
			" SELECT p.* " .
			"   FROM pm_Project p, pm_PublicInfo i " .
			"  WHERE i.Project = p.pm_ProjectId" .
			"    AND IFNULL(i.IsProjectInfo, 'N') = 'Y' " .
			"    AND IFNULL(p.IsClosed, 'N') = 'N'" .
			"  ORDER BY p.Rating DESC ".
			"  LIMIT ".$limit;

		return $this->createSQLIterator( $sql );
	}

	function getLatestMostUsedPublicIt( $limit = 10, $page = 0 )
	{
		$sql = 
			" SELECT p.* " .
			"   FROM pm_Project p, pm_PublicInfo i " .
			"  WHERE i.Project = p.pm_ProjectId" .
			"    AND IFNULL(i.IsProjectInfo, 'N') = 'Y' " .
			"    AND p.Description IS NOT NULL ".
			"    AND (SELECT COUNT(1) FROM ObjectChangeLog l, pm_Participant r " .
			"		   WHERE r.Project = p.pm_ProjectId" .
			"			 AND r.pm_ParticipantId = l.Author) > 2 ".
			"  ORDER BY (SELECT COUNT(1) FROM co_ProjectSubscription cos" .
			"		      WHERE cos.Project = p.pm_ProjectId) DESC ".
			"  LIMIT ".$limit." OFFSET ".( $page * $limit );

		return $this->createSQLIterator( $sql );
	}

	function getLatestMostUsedPublicCount()
	{
		$sql = 
			" SELECT COUNT(1) cnt " .
			"   FROM pm_Project p, pm_PublicInfo i " .
			"  WHERE i.Project = p.pm_ProjectId" .
			"    AND IFNULL(i.IsProjectInfo, 'N') = 'Y' " .
			"    AND p.Description IS NOT NULL ".
			"    AND (SELECT COUNT(1) FROM ObjectChangeLog l, pm_Participant r " .
			"		   WHERE r.Project = p.pm_ProjectId" .
			"			 AND r.pm_ParticipantId = l.Author) > 2 ";
			
		$it = $this->createSQLIterator( $sql );

		return $it->get('cnt');
	}

	function getAllPublicIt()
	{
		$sql = 
			" SELECT p.* " .
			"   FROM pm_Project p, pm_PublicInfo i " .
			"  WHERE i.Project = p.pm_ProjectId" .
			"    AND IFNULL(i.IsProjectInfo, 'N') = 'Y'";

		return $this->createSQLIterator( $sql );
	}

	function getProductSiteIt()
	{
		$sql = 
			" SELECT p.* " .
			"   FROM pm_Project p, pm_ProjectCreation c " .
			"  WHERE c.Project = p.pm_ProjectId" .
			"    AND c.Methodology = 5 ".
			"  ORDER BY p.RecordCreated DESC ";

		return $this->createSQLIterator( $sql );
	}

	function getDefaultAttributeValue( $attr )
	{
		switch( $attr )
		{
		    case 'Caption':
		    	return translate('Проект').' '.($this->getRegistry()->Count() + 1);
		    	
		    case 'CodeName':
		    	return 'project'.($this->getRegistry()->Count() + 1);
		    	
		    case 'OrderNum':
		    	return '';
		    	
		    case 'Importance':
		    	return 3;
		    	
		    default:
		    	return parent::getDefaultAttributeValue( $attr );
		}
	}
	
	function add_parms( $parms )
	{
		global $model_factory;
		
		$id = parent::add_parms( $parms );
		
		$public_cls = new Metaobject('pm_PublicInfo');

		$parms = array();
		
		$parms['Project'] = $id;
		$parms['VPD'] = ModelProjectOriginationService::getOrigin($id);
		
		$public_cls->add_parms($parms);
		
		return $id;
	}
}
