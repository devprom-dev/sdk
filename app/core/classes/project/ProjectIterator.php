<?php

class ProjectIterator extends OrderedIterator
{
    var $methodology_it_cache;
    
	function IsTender()
	{
		return $this->get('IsTender') == 'Y';
	}

 	function IsPortfolio()
	{
		return $this->get('IsTender') == 'F';
	}

  	function IsProgram()
	{
		return $this->get('IsTender') == 'Y';
	}
	
	function getParentIt()
	{
	    global $model_factory;
	    
		if ( $this->IsPortfolio() )
	    {
	        return $this->object->createCachedIterator(array());
	    }
	    
	    $project_it = $this->getRef('LinkedProject');
	    
	    while ( !$project_it->end() )
	    {
	        if ( $project_it->IsProgram() )
	        {
	            return $project_it;
	        }
	        
	        $project_it->moveNext();
	    }
	    
	    $portfolio_it = $model_factory->getObject('Portfolio')->getAll();
	    
	    while ( !$portfolio_it->end() )
	    {
	        $project_ids = preg_split('/,/',$portfolio_it->get('LinkedProject'));
	        
	        if ( in_array($this->getId(), $project_ids) ) return $portfolio_it;
	        
	        $portfolio_it->moveNext();
	    }
	    
	    $portfolio_it->moveTo('CodeName', 'my');
	    
	    return $portfolio_it->getId() != '' ? $portfolio_it : $this->object->createCachedIterator(array());
	}
	
	function HasProductSite()
	{
		return $this->get('Tools') != '';
	}

	function getTenderIt()
	{
		global $model_factory;
		
		$part = $model_factory->getObject('co_TenderParticipant');
		$part_it = $part->getByRef('Project', $this->getId());
		
		if ( $part_it->count() > 0 )
		{
			return $part_it->getRef('Tender');
		}
		else
		{
			return null;
		}
	}
	
 	function IsPublic() 
 	{
 		return $this->get('IsProjectInfo') == 'Y';
 	}
 	 
 	function IsPublicBlog() {
 		return $this->get('IsBlog') == 'Y';
 	}
 	
 	function IsPublicParticipants() {
 		return $this->get('IsParticipants') == 'Y';
 	}
 	
 	function IsPublicReleases() {
 		return $this->get('IsReleases') == 'Y';
 	}
 	
 	function IsPublicChangeRequests() {
 		return $this->get('IsChangeRequests') == 'Y';
 	}
 	
 	function IsPublicDocumentation() {
 		return $this->get('IsPublicDocumentation') == 'Y';
 	}

 	function IsPublicArtefacts() {
 		return $this->get('IsPublicArtefacts') == 'Y';
 	}
 	
	function IsUsedPolls()
	{
		return $this->get('IsPollUsed') != 'N';
	}
	
 	function IsUserParticipate( $user_id ) 
 	{
 		$sql = " SELECT p.* " .
 				"  FROM pm_Participant p " .
 				" WHERE p.Project = ".$this->getId().
 				"   AND p.SystemUser = ".$user_id.
 				"   AND IFNULL(IsActive,'Y') = 'Y' ";
 		
 		return getFactory()->getObject('pm_Participant')->createSQLIterator($sql)->count() > 0;
 	}
 	
 	function IsActive()
 	{
 		return $this->get('IsClosed') != 'Y';
 	}
 	
 	function IsInRussian()
 	{
 		return $this->get('Language') == 1;
 	}
 	
 	function IsInEnglish()
 	{
 		return $this->get('Language') == 2;
 	}

 	function getParticipantIt()
 	{
 		$part = getFactory()->getObject('pm_Participant');
 		
 		$part->defaultsort = 'Caption ASC';

 		$it = $part->getByRef2('Project', $this->getId(),
 			"IFNULL(IsActive,'Y')", "Y");

 		return $it;
 	}
 	
 	function getParticipantsCount()
 	{
 		global $model_factory;
 		
 		$part = $model_factory->getObject('pm_Participant');

 		return $part->getByRefArrayCount(
			array( 'Project' => $this->getId(),
 				   "IFNULL(IsActive,'Y')" => "Y" ) );
 	}

 	function getParticipantForUserIt( $user_it )
 	{
 		$sql = " SELECT p.* " .
 				"  FROM pm_Participant p " .
 				" WHERE p.Project = ".$this->getId().
 				"   AND p.SystemUser = ".$user_it->getId().
 				"   AND IFNULL(IsActive,'Y') = 'Y' ";
 		
 		return getFactory()->getObject('pm_Participant')->createSQLIterator($sql);
 	}
 	

 	function getSubscribersIt( $limit = 0 )
 	{
 		$sql = " SELECT u.* FROM co_ProjectSubscription s, cms_User u " .
 			   "  WHERE s.Project = ".$this->getId().
 			   "    AND s.SystemUser = u.cms_UserId ".
 			   "  ORDER BY RAND() DESC, PhotoExt DESC " .
 			   ($limit > 0 ? " LIMIT ".$limit : "");
 			    
 		return getFactory()->getObject('cms_User')->createSQLIterator($sql);
 	}

 	function getSubscribersCount()
 	{
		return getFactory()->getObject('co_ProjectSubscription')->getByRefArrayCount(
			array( 'Project' => $this->getId() ) );
 	}

 	function IsSubscribed()
 	{
 		global $model_factory;
 		
 		$sub = $model_factory->getObject('co_ProjectSubscription');
		
		return $sub->getByRefArrayCount(
			array ( 'Project' => $this->getId(),
					'SystemUser' => getSession()->getUserIt()->getId() ) ) > 0;
 	}

 	function getLeadIt()
 	{
 		return getFactory()->getObject('pm_Participant')->getLeadTeam($this->getId());
 	}
 	
 	function getVotedIt()
 	{
 		return getFactory()->getObject('co_Rating')->getVotedIt($this);
 	}
 	
 	function getDownloadedIt()
 	{
 		$user = getFactory()->getObject('cms_User');

 		$sql = " SELECT u.*" .
 			   "   FROM cms_User u " .
 			   "  WHERE u.cms_UserId " .
 			   "		IN (SELECT ac.SystemUser FROM pm_Artefact a, pm_DownloadAction da, pm_DownloadActor ac " .
 			   "    		 WHERE ac.DownloadAction = da.pm_DownloadActionId" .
 			   "    		   AND da.ObjectId = a.pm_ArtefactId" .
 			   "    		   AND da.EntityRefName = 'pm_Artefact'" .
 			   "    		   AND a.Project = ".$this->getId().") ";
 			   
 		return $user->createSQLIterator($sql);
 	}

 	function getPlannedVersionIt()
 	{
 		$version_it = $this->getRef('Version');
 		
 		$sql = 
 			" SELECT r.*" .
 			"   FROM pm_Version r, pm_Project p " .
 			"  WHERE r.Project = ".$this->getId().
 			"    AND p.pm_ProjectId = r.Project ".
 			"    AND r.Caption >= '".$version_it->get('Caption')."'" .
 			"  ORDER BY r.Caption ASC";
 			
 		return getFactory()->getObject('pm_Version')->createSQLIterator($sql);
 	}
 	
 	function getBlogId()
 	{
 		return $this->get('Blog');
 	}
 	
 	function getBlogIt()
 	{
 		return $this->getRef('Blog');
 	}

 	function getMethodologyIt()
 	{
 		global $model_factory;
 		
 		if ( isset($this->methodology_it_cache[$this->getId()]) ) return $this->methodology_it_cache[$this->getId()];

		$methodology = $model_factory->getObject('pm_Methodology');

 		if ( $this->getId() < 1 ) return $methodology->getEmptyIterator(); 
		
		$this->methodology_it_cache[$this->getId()] = $methodology->getByRef('Project', $this->getId())->copy();
		
		return $this->methodology_it_cache[$this->getId()];
 	}
 	
 	function invalidateCache()
 	{
 		$this->methodology_it_cache = array();
 	}
 	
	function getTeamVelocity()
	{
		if ( $this->getMethodologyIt()->HasPlanning() )
		{
			$sql = " SELECT IFNULL(AVG(m2.MetricValue), 0) Velocity " .
			 	   "   FROM pm_IterationMetric m2, pm_Release r " .
			 	   "  WHERE m2.Iteration = r.pm_ReleaseId " .
			 	   "	AND m2.Metric = 'Velocity' ".
			 	   "    AND r.Project = ".$this->getId().
			 	   "    AND m2.MetricValue > 0 ".
			 	   "  ORDER BY r.RecordCreated DESC LIMIT 3";
		}
		else
		{
			$sql = " SELECT IFNULL(AVG(m2.MetricValue), 0) Velocity " .
			 	   "   FROM pm_VersionMetric m2, pm_Version r " .
			 	   "  WHERE m2.Version = r.pm_VersionId " .
			 	   "	AND m2.Metric = 'Velocity' ".
			 	   "    AND r.Project = ".$this->getId().
			 	   "    AND m2.MetricValue > 0 ".
			 	   "  ORDER BY r.RecordCreated DESC LIMIT 3";
		}

		$it = $this->object->createSQLIterator( $sql );
		if ( $it->get('Velocity') == 0 )
		{
			return $this->getPlannedTeamVelocity();
		}
		else
		{
			return round($it->get('Velocity'), 1);
		}
	}
	
	function getVelocityDevider()
	{
	    $velocity = $this->getTeamVelocity();
	    
		$methodology_it = $this->getMethodologyIt(); 
		
		if ( $methodology_it->HasFixedRelease() )
		{
		    $devider = $methodology_it->getReleaseDuration() * $this->getDaysInWeek();
		    
		    if ( $devider > 0 )
		    {
		        $velocity /= $devider;
		    }
		    else
		    {
		        $velocity = 0;
		    }
		}
	    
		return $velocity;
	}
	
	function getPlannedTeamVelocity()
	{
		$sql = 'SELECT ROUND(SUM(r.Capacity)) Capacity '.
			   '  FROM pm_Participant p, pm_ParticipantRole r, pm_ProjectRole l '.
			   ' WHERE r.Project = '.$this->getId().
			   '   AND r.Participant = p.pm_ParticipantId '.
			   "   AND p.IsActive = 'Y' " .
			   "   AND r.ProjectRole = l.pm_ProjectRoleId" .
			   "   AND l.ReferenceName NOT IN ('lead', 'client') ";
			   
		$it = $this->object->createSQLIterator( $sql );
		
		return round($it->get('Capacity'), 1);
	}
	
	function getTeamEfficiency()
	{
		$sql = " SELECT IFNULL(AVG(m2.MetricValue), 0) Efficiency " .
		 	   "   FROM pm_IterationMetric m2, pm_Release r " .
		 	   "  WHERE m2.Iteration = r.pm_ReleaseId " .
		 	   "	AND m2.Metric = 'Efficiency' ".
		 	   "    AND r.Project = ".$this->getId().
		 	   "    AND m2.MetricValue > 0 ";
		
		$it = $this->object->createSQLIterator( $sql );
		return round($it->get('Efficiency'));
	}

	function getTeamBugsPercent()
	{
		$sql = 	" SELECT IFNULL(AVG(m2.MetricValue), 0) BugsInWorkload " .
		 		"   FROM pm_VersionMetric m2, pm_Version v " .
		 		"  WHERE m2.Version = v.pm_VersionId" .
		 		"    AND v.Project = ".$this->getId().
		 		"	 AND m2.Metric = 'BugsInWorkload' " .
		 		"    AND m2.MetricValue > 0 ".
		 	    "  ORDER BY v.RecordCreated DESC" .
		 	    "  LIMIT 3";
		
		$it = $this->object->createSQLIterator( $sql );
		return round($it->get('BugsInWorkload'));
	}
	
	function getTeamEstimationError()
	{
		$sql = 	" SELECT IFNULL(AVG(m2.MetricValue), 0) EstimationError " .
		 		"   FROM pm_VersionMetric m2, pm_Version v " .
		 		"  WHERE m2.Version = v.pm_VersionId" .
		 		"    AND v.Project = ".$this->getId().
		 		"	 AND m2.Metric = 'EstimationError' " .
		 		"    AND m2.MetricValue > 0 ".
		 	    "  ORDER BY v.RecordCreated DESC" .
		 	    "  LIMIT 3";
		
		$it = $this->object->createSQLIterator( $sql );
		return round($it->get('EstimationError'));
	}

	function getActualCost()
	{
		$sql = 	" SELECT IFNULL(SUM(m2.MetricValue), 0) Cost " .
		 		"   FROM pm_VersionMetric m2, pm_Version v " .
		 		"  WHERE m2.Version = v.pm_VersionId" .
		 		"    AND v.Project = ".$this->getId().
		 		"	 AND m2.Metric = 'ActualCost' ";
		
		$it = $this->object->createSQLIterator( $sql );
		return round($it->get('Cost'));
	}

	function getTotalWorkload() 
	{
		$request = getFactory()->getObject('pm_ChangeRequest');
		$request->addFilter( new FilterAttributePredicate('Project', $this->getId()) );
		
		return array_shift(
				$this->getMethodologyIt()->getEstimationStrategy()->getEstimation( $request, 'Estimation')
		); 
	}
	
	function getRemoveKey()
	{
		return md5(INSTALLATION_UID.$this->get('CodeName').date('%Y-%m-%d-%H').$this->getId().INSTALLATION_UID);
	}
	
	function getFeedbackAuthKey()
	{
		return md5(INSTALLATION_UID.$this->get('CodeName').INSTALLATION_UID);
	}

	function getRating()
	{
		return $this->get('Rating');
	}
	
	function getMetricsDate()
	{
		$sql = " SELECT MAX(m.RecordModified) LastDate " .
			   "   FROM pm_VersionMetric m, pm_Version v " .
			   "  WHERE m.Version = v.pm_VersionId " .
			   "    AND v.Project = ".$this->getId();
			   
		$it = $this->object->createSQLIterator( $sql );
		
		return $it->getDateTimeFormat('LastDate');
	}
	
	function getRecentChangeIt( $limit = 10 )
	{
		global $model_factory;
		
		$changes = $model_factory->getObject('ChangeLog');
		
		$sql = " SELECT ch.*, i.Project, i.IsKnowledgeBase, i.IsBlog, " .
			   "		i.IsChangeRequests, i.IsParticipants, i.IsPublicArtefacts " .
			   "   FROM ObjectChangeLog ch, pm_PublicInfo i " .
			   "  WHERE i.VPD = ch.VPD AND ch.VPD <> '' ".
			   " 	AND i.Project = ".$this->getId().
			   "  ORDER BY ch.RecordCreated DESC" .
			   "  LIMIT ".$limit;
			   
		return $changes->createSQLIterator( $sql );
	}
	
	function getPostIt( $limit = 20 )
	{
		global $model_factory;
		
		$sql = " SELECT p.*, j.pm_ProjectId Project " .
			   "   FROM BlogPost p, pm_Project j, pm_PublicInfo i" .
			   "  WHERE p.Blog = j.Blog " .
			   "    AND j.pm_ProjectId = i.Project " .
			   "    AND j.pm_ProjectId = " .$this->getId().
			   "  ORDER BY p.RecordCreated DESC" .
			   "  LIMIT ".$limit;
		
		$post = $model_factory->getObject('BlogPost');
		return $post->createSQLIterator($sql);
	}

	function getRelatedPostIt( $limit = 20 )
	{
		global $model_factory;
		
		$sql = " SELECT p.*, j.pm_ProjectId Project " .
			   "   FROM BlogPost p, pm_Project j " .
			   "  WHERE p.Blog = j.Blog " .
			   "    AND j.pm_ProjectId IN (".join($this->idsToArray(), ', ').") ".
			   "  ORDER BY p.RecordCreated DESC" .
			   "  LIMIT ".$limit;

		$post = $model_factory->getObject('BlogPost');
		
		return $post->createSQLIterator($sql);
	}
	
	function getTagsIt()
	{
		global $model_factory;
		$protag = $model_factory->getObject('pm_ProjectTag');
		
		return $protag->getByRefArray(
			array( 'Project' => $this->idsToArray() ) 
			);
	}
	
	function getSitePageIt( $page )
	{
		global $model_factory;
		
		$sql = "SELECT p.* " .
				" FROM WikiPage p " .
				"WHERE p.ReferenceName = " .getFactory()->getObject('ProjectPage')->getReferenceName().
				"  AND (SELECT COUNT(1) FROM WikiTag wt, Tag t " .
				"		 WHERE wt.Wiki = p.WikiPageId AND t.TagId = wt.Tag " .
				"		   AND t.Caption IN ('sitepage', '".$page."') ) > 1 ".
				"  AND p.Project = ".$this->getId().
				" ORDER BY p.WikiPageId ASC";

 		$page = $model_factory->getObject('ProjectPage');
 		$page_it = $page->createSQLIterator( $sql );

 		return $page_it;
	}

	function getProductPageIt()
	{
		return $this->getSitePageIt( 'product' );
	}

	function getDaysInWeek()
	{
		if ( $this->get('DaysInWeek') < 1 )
		{
			return 5;
		}
		else
		{
			return $this->get('DaysInWeek');
		}
	}
	
	function getDefaultNotificationType()
	{
		switch( $this->get('Tools') )
		{
		    case 'ticket_ru.xml':
		    case 'ticket_en.xml':
		    	return 'system';
		    	
		    case 'incidents_ru.xml':
		    case 'incidents_en.xml':
		    	return '';
		    	
		    default:
				return 'every1hour';    	
		}
	}
}
