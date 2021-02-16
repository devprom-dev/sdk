<?php

 define('LDAP_JOB_NAME', 'ldapupdateattributes');

 ////////////////////////////////////////////////////////////////////////////
 class LDAPComplete extends CommandForm
 {
 	function getLogger()
 	{
 		try
 		{
 			return Logger::getLogger('LDAP');
 		}
 		catch( Exception $e)
 		{
 			error_log('Unable initialize logger: '.$e->getMessage());
 			
 			return null;
 		}
 	}
 	
 	function validate()
 	{
 		return true;
 	}
 	
 	function create()
	{
		global $_REQUEST, $model_factory;
		
		if ( $_REQUEST['SubmitJob'] == 'on' ) 
		{
			$job = $model_factory->getObject('co_ScheduledJob');
			$job_it = $job->getByRef('ClassName', LDAP_JOB_NAME);
			
			if ( $job_it->count() < 1 )
			{
				$job->add_parms( array (
					'Caption' => text(2782),
					'ClassName' => LDAP_JOB_NAME,
					'Minutes' => '*',
					'Hours' => '*',
					'Days' => '*',
					'WeekDays' => '*',
					'IsActive' => 'Y'
				));	
			}
		}
		
		$this->replyRedirect( '/admin/users.php' );
	}
 }
 
?>