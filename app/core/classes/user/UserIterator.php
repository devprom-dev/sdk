<?php

class UserIterator extends OrderedIterator
{
 	function get( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'Skills':
 			case 'Tools':
 			case 'HourCost':
 				
 			    $it = $this->getProjectParticipanceRoleIt();
 				
 			    return $it->get( $attribute );
 				
 			default:
	 			return parent::get( $attribute );
 		}
 	}
 	
 	function getDisplayName()
 	{
		return parent::getDisplayName();
 	}
 	
 	/*
 	 * returns a hash value to reset password of a user
 	 */
 	function getResetPasswordKey()
 	{
 		return md5($this->getId().RESET_KEY.
			$this->get_native('Password').date('%Y-%d-%m'));
 	}
 	
 	/*
 	 * defines if a user was blocked automatically or by admin,
 	 * blocked user can't login into the system
 	 */
 	function IsBlocked()
 	{
 		if ( !isset($this->blacklist) )
 		{
 			$this->blacklist = getFactory()->getObject('cms_BlackList');
 		}
 		
 		$cnt = $this->blacklist->getByRefArrayCount(
 			array (
 				'SystemUser' => $this->getId()
 				)
 			);
 			
 		return $cnt > 0; 
 	}
 	
 	/*
 	 * defines if a user was activated by it's email, 
 	 * every new use is not activated by default
 	 */
 	function IsActivated()
 	{
 		return $this->get('IsActivated') == 'Y';
 	}
 	
 	function IsAdministrator()
 	{
 		return $this->get('IsAdministrator') == 'Y';
 	}
 	
 	function IsReal()
 	{
 		return $this->get('Caption') != 'anonymous' && $this->get('Caption') != '';
 	}
 	
 	/*
 	 * returns activation key to activate a user by email securely
 	 */
 	function getActivationKey()
 	{
 		return md5(RESET_KEY.$this->getId().$this->get_native('Login'));
 	}
 	
 	function getAverageEfficiency()
 	{
 		$sql = 	" SELECT IFNULL(AVG(m.MetricValue), 0) Efficiency " .
		 		"   FROM pm_ParticipantMetrics m, pm_Participant p " .
		 		"  WHERE m.Participant = p.pm_ParticipantId" .
		 		"    AND m.Metric = 'Efficiency' " .
		 		"    AND p.SystemUser = ".$this->getId().
		 		"    AND (SELECT COUNT(1) FROM pm_Task t WHERE t.Assignee = m.Participant) > 10 ";
 		
 		$it = $this->object->createSQLIterator($sql);
 		
 		return round($it->get('Efficiency'));
 	}

	/*
	 * returns personal velocity for the participant 
	 */
	function getVelocity()
	{
 		$sql = 	" SELECT IFNULL(MAX(m.MetricValue), 0) Velocity " .
		 		"   FROM pm_ParticipantMetrics m, pm_Participant p " .
		 		"  WHERE m.Participant = p.pm_ParticipantId" .
		 		"    AND m.Metric = 'Velocity' " .
		 		"    AND p.SystemUser = ".$this->getId();
 		
 		$it = $this->object->createSQLIterator($sql);
 		
 		return round($it->get('Velocity'));
	}

 	function getMaxEfficiency()
 	{
 		$sql = 	" SELECT IFNULL(MAX(m.MetricValue), 0) Efficiency " .
		 		"   FROM pm_ParticipantMetrics m, pm_Participant p " .
		 		"  WHERE m.Participant = p.pm_ParticipantId" .
		 		"    AND m.Metric = 'Efficiency' " .
		 		"    AND p.SystemUser = ".$this->getId().
		 		"    AND (SELECT COUNT(1) FROM pm_Task t WHERE t.Assignee = m.Participant) > 10 ";
 		
 		$it = $this->object->createSQLIterator($sql);
 		
 		return round($it->get('Efficiency'));
 	}
 	
 	function getRating()
 	{
 		return $this->get('Rating');
 	}

	function getMissedCommunityRoleIt()
	{
		$sql = " SELECT cr.* " .
			   "   FROM co_CommunityRole cr " .
			   " WHERE NOT EXISTS (SELECT 1 FROM co_UserRole ur " .
			   "				    WHERE ur.SystemUser = ".$this->getId()." " .
			   "                      AND ur.CommunityRole = cr.co_CommunityRoleId)";

		return getFactory()->getObject('co_CommunityRole')->createSQLIterator( $sql );
	}

	function hasRole( $role_id )
	{
		return getFactory()->getObject('co_UserRole')->getByRefArrayCount( 
			array( 'SystemUser' => $this->getId(), 'CommunityRole' => $role_id ) ) > 0; 
		
	}
	
	function addRole( $role_id )
	{
	 	getFactory()->getObject('co_UserRole')->add_parms(
	 		array( 'SystemUser' => $this->getId(),
	 			   'CommunityRole' => $role_id ) );
	 			   
	 	switch ( $role_id )
	 	{
	 		case 2:
			 	getFactory()->getObject('co_ProjectParticipant')->add_parms(
			 		array( 'SystemUser' => $this->getId() )
			 	);
	 			
	 			break;
	 	}
	}
	
	function deleteRole( $role_id )
	{
	 	$role_it = getFactory()->getObject('co_UserRole')->getByRefArray(
	 		array( 'SystemUser' => $this->getId(),
	 			   'CommunityRole' => $role_id ) );
	 	
	 	if ( getFactory()->getAccessPolicy()->can_delete($role_it) ) return;
	 	
 		$role->delete($role_it->getId());   

	 	switch ( $role_id )
	 	{
	 		case 2:
				$participance_it = getFactory()->getObject('co_ProjectParticipant')->getByRefArray(
			 		array( 'SystemUser' => $this->getId() )
			 	);
	 			
	 			$participance->delete( $participance_it->getId() );
	 			break;
	 	}
	}

	function getProjectParticipanceRoleIt()
	{
		$participance = $this->object->
			model_factory->getObject('co_ProjectParticipant');
			
		return $participance->getByRef('SystemUser', $this->getId());
	}
	
	function getNewsKey()
	{
		return md5( INSTALLATION_UID.$this->get('Email').CUSTOMER_UID.$this->get('Login').$this->getId() );
	}
	
	function getApiKey()
	{
		return md5($this->getId().$this->get('Login').INSTALLATION_UID.$this->get('Password'));
	}
	
	function getParticipantIt()
	{
		return getFactory()->getObject('pm_Participant')->getRegistry()->Query( 
				array ( new FilterAttributePredicate('SystemUser', $this->getId()) )
		);
	}
}