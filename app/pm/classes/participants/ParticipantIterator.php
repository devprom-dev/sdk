<?php

class ParticipantIterator extends OrderedIterator
{
	function get( $attr_name ) 
	{
		global $model_factory;		
				
		switch ( strtolower($attr_name) )
		{
			case 'password':
				
			    $user_it = $this->getRef('SystemUser');
				
				if ( is_object($user_it) )
				{
					return $user_it->get_native($attr_name);
				}
				else
				{
					return '';
				}
			
			case 'notification':
				
			    return $this->get_native( $attr_name );
			    				
			default:
				
			    return parent::get( $attr_name );
		}
	} 
	
 	function get_native( $attr_name ) 
	{
		global $model_factory;		
				
		switch ( strtolower($attr_name) )
		{
			case 'notification':
				
			    $notification = $model_factory->getObject('Notification');
				
			    return $notification->getType($this);
				
			default:
				
			    return parent::get_native( $attr_name );
		}
	} 
	
 	function getBaseRoles() 
 	{
 		if ( $this->getId() < 1 )
 		{
			return array('guest' => true);
 		}
 		
 		$names = preg_split('/,/', $this->get('ProjectRoleReferenceName'));
 		
 		$roles = array();
 		
 		foreach( $names as $name )
 		{
 		    $roles[$name] = true;
 		}
 		
 		return $roles;
	}
	
 	function getRoles() 
 	{
 		if ( $this->getId() < 1 ) return array(0); // guest

 		if ( $this->get('ProjectRole') == '' ) {
			$roles = getFactory()->getObject('pm_ProjectRole')->getByRef('ReferenceName', 'linkedguest')->getId();
			if ( $roles == '' ) return array(0); // guest
 		}
		else {
			$roles = $this->get('ProjectRole');
		}

 		return array_filter( preg_split('/,/', $roles),
				function($value) {
					return $value > 0;
				}
		);
	}

	function getProjects()
	{
		$sql = 'SELECT DISTINCT prj.* FROM pm_Project prj, pm_Participant part ' .
			   ' WHERE part.SystemUser = '.getSession()->getUserIt()->getId().
			   '   AND part.Project = prj.pm_ProjectId ' .
			   "   AND part.IsActive = 'Y' ";

		return getFactory()->getObject('pm_Project')->createSQLIterator($sql);
	}
	
	function isLead()
	{
		$roles = $this->getBaseRoles();

		return $roles['lead'] == true;
	}
	
	function isDeveloper()
	{
		$roles = $this->getBaseRoles();
		
		return $roles['developer'] == true;
	}

	function isTester()
	{
		$roles = $this->getBaseRoles();
		
		return $roles['tester'] == true;
	}

	function isClient()
	{
		$roles = $this->getBaseRoles();
		
		return $roles['client'];
	}

	function isActive()
	{
		return $this->get('IsActive') <> 'N';
	}
	
	/*
	 * returns total capacity of the participant
	 */
	function getCapacity()
	{
		return $this->get('Capacity');
	}
	
	/*
	 * returns capacity of the participant per role
	 */
	function getCapacityByRoles( $roles )
	{
		$sql = " SELECT IFNULL(ROUND(SUM(r.Capacity)), 0) Result " .
				"  FROM pm_ParticipantRole r, pm_ProjectRole p" .
				" WHERE r.Participant = ".$this->getId().
				"   AND r.Project = ".$this->get('Project').
				"   AND r.ProjectRole = p.pm_ProjectRoleId" .
				"   AND p.ProjectRoleBase IN ( ".join(',', $roles)." )";
		
		$it = $this->object->createSQLIterator($sql);
		return $it->get('Result');
	}

	/*
	 * returns capacity of the participant per role
	 */
	function getCapacityByRole( $role_id )
	{
		return $this->getCapacityByRoles( array($role_id) );
	}

	function getDevelopmentCapacity()
	{
		return $this->getCapacityByRole( 2 );
	}
	
	function getTestingCapacity()
	{
		return $this->getCapacityByRole( 3 );
	}

	/*
	 * returns work efficiency for the participant 
	 */
	function getEfficiency()
	{
		global $project_it;
		
		 $sql = " SELECT IFNULL(AVG(m.MetricValue), 0) Efficiency " .
		 		"   FROM pm_ParticipantMetrics m " .
		 		"  WHERE m.Participant = " .$this->getId().
		 		"	 AND m.Metric = 'Efficiency'" .
		 		"    AND m.MetricValue > 0" .
		 		"  ORDER BY m.RecordCreated DESC";
		 		
		 return getFactory()->getObject('pm_ParticipantMetrics')->createSQLIterator($sql)->get('Efficiency');
	}
	
	/*
	 * returns work efficiency for the participant 
	 */
	function getReleaseEfficiency( $version_it )
	{
		global $project_it;
		
		 $sql = " SELECT AVG(m.MetricValue) Efficiency " .
		 		"   FROM pm_Release r, " .
		 		"	     pm_ParticipantMetrics m " .
		 		"  WHERE r.Version = ".$version_it->getId().
 		   	    "    AND r.StartDate < NOW() ".			   
		 		"    AND m.Participant = " .$this->getId().
		 		"    AND m.Iteration = r.pm_ReleaseId" .
		 		"	 AND m.Metric = 'Efficiency'";
		 		
		 return getFactory()->getObject('pm_ParticipantMetrics')->createSQLIterator($sql)->get('Efficiency');
	}

	/*
	 * returns work efficiency for the participant 
	 */
	function getIterationEfficiency( $iteartion_it )
	{
		global $project_it;
		
		 $sql = " SELECT AVG(m.MetricValue) Efficiency " .
		 		"   FROM pm_ParticipantMetrics m " .
		 		"  WHERE m.Iteration = ".$iteartion_it->getId().
 		   	    "    AND m.Participant = " .$this->getId().
		 		"	 AND m.Metric = 'Efficiency'";
		 		
		 return getFactory()->getObject('pm_ParticipantMetrics')->createSQLIterator($sql)->get('Efficiency');
	}

	/*
	 * returns personal velocity for the participant 
	 */
	function getVelocity()
	{
		global $project_it;
		
		 $sql = " SELECT IFNULL(AVG(m.MetricValue), 0) Velocity " .
		 		"   FROM pm_Release r, " .
		 		"	     pm_ParticipantMetrics m " .
		 		"  WHERE r.Project = ".$project_it->getId().
 		   	    "    AND r.StartDate < NOW() ".			   
		 		"    AND m.Participant = " .$this->getId().
		 		"    AND m.Iteration = r.pm_ReleaseId" .
		 		"	 AND m.Metric = 'Velocity'";
		 		
		 return getFactory()->getObject('pm_ParticipantMetrics')->createSQLIterator($sql)->get('Velocity');
	}

	/*
	 * returns personal velocity for the participant 
	 */
	function getReleaseVelocity( $version_it )
	{
		global $project_it;
		
		 $sql = " SELECT AVG(m.MetricValue) Velocity " .
		 		"   FROM pm_Release r, " .
		 		"	     pm_ParticipantMetrics m " .
		 		"  WHERE r.Version = ".$version_it->getId().
 		   	    "    AND r.StartDate < NOW() ".			   
		 		"    AND m.Participant = " .$this->getId().
		 		"    AND m.Iteration = r.pm_ReleaseId" .
		 		"	 AND m.Metric = 'Velocity'";
		 		
		 return getFactory()->getObject('pm_ParticipantMetrics')->createSQLIterator($sql)->get('Velocity');
	}
	
	/*
	 * returns velocity for the participant 
	 */
	function getIterationVelocity( $iteartion_it )
	{
		global $project_it;
		
		 $sql = " SELECT AVG(m.MetricValue) Velocity " .
		 		"   FROM pm_ParticipantMetrics m " .
		 		"  WHERE m.Iteration = ".$iteartion_it->getId().
 		   	    "    AND m.Participant = " .$this->getId().
		 		"	 AND m.Metric = 'Velocity'";
		 		
		 return getFactory()->getObject('pm_ParticipantMetrics')->createSQLIterator($sql)->get('Velocity');
	}

	function getAverageInvolvement( $metric )
	{
		global $project_it;
		
		 $sql = " SELECT AVG(m.MetricValue) Efficiency " .
		 		"   FROM pm_ParticipantMetrics m " .
		 		"  WHERE m.Participant = " .$this->getId().
		 		"	 AND m.Metric = '".$metric."'" .
		 		"  ORDER BY m.RecordCreated DESC " .
		 		"  LIMIT 5 ";
		 		
		 return getFactory()->getObject('pm_ParticipantMetrics')->createSQLIterator($sql)->get('Efficiency');
	}

	function getDevelopmentInvolvement()
	{
		 return $this->getAverageInvolvement( 'DevelopmentInvolvement' );
	}
	
	function getTestingInvolvement()
	{
		 return $this->getAverageInvolvement( 'TestingInvolvement' );
	}

	function getMetrics( $release_it, $metrics )
	{
		$addition = array();
		
		 for ( $i = 0; $i < count($metrics); $i++ )
		 {
		 	array_push( $addition,
		 		"		 (SELECT MAX(IFNULL(m2.MetricValue, 0)) " .
		 		"           FROM pm_ParticipantMetrics m2 " .
		 		"          WHERE m2.Participant = m.Participant" .
		 		"            AND m2.Iteration = m.Iteration" .
		 		"		     AND m2.Metric = '".$metrics[$i]."') ".$metrics[$i] );
		 }
		 
		 if ( $release_it->count() < 1 )
		 {
		 	return null;
		 }
		 
		 $sql = " SELECT m.Iteration, v.Caption, r.ReleaseNumber, " .
		 			     join(', ', $addition).
		 		"   FROM pm_ParticipantMetrics m, pm_Release r, pm_Version v " .
		 		"  WHERE m.Participant = ".$this->getId().
		 		"    AND m.Iteration = r.pm_ReleaseId ".
		 		//( $release_it->count() < 1 ? "" : " AND m.Iteration IN (" .join(',', $release_it->idsToArray()).') ').
		 		" AND m.Iteration IN (" .join(',', $release_it->idsToArray()).') '.
		 		"    AND v.pm_VersionId = r.Version ".
		 		"  GROUP BY m.Iteration" .
		 		"  ORDER BY m.Iteration ";
		 		
		 return getFactory()->getObject('pm_ParticipantMetrics')->createSQLIterator($sql);
	}
}