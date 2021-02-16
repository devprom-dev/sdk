<?php
include_once SERVER_ROOT_PATH.'admin/classes/ldap/LDAP.php';

class LDAPUpdateAttributes extends Command
{
 	function getLogger()
 	{
 		try {
 			return Logger::getLogger('LDAP');
 		}
 		catch( Exception $e) {
 			error_log('Unable initialize logger: '.$e->getMessage());
 			return null;
 		}
 	}
	
 	function execute()
	{
		$this->logStart();
		
		$step = 10;
		
		$job = getFactory()->getObject('cms_BatchJob');
		$job_it = $job->getByRefArray(
			array ('Caption' => get_class($this)), 1
			);

		if ( $job_it->count() > 0 )
		{
			$job->delete($job_it->getId());
			$this->processChunk( preg_split('/,/', $job_it->get('Parameters')) );
        }
		else
		{
			$user_it = getFactory()->getObject('User')->getRegistry()->Query(
			    array(
			        new FilterAttributeNotNullPredicate('LDAPUID')
                )
            );
			$ids = $user_it->idsToArray();
			
			$chunks = array_chunk($ids, $step);
			$immediate_chunk = array_shift( $chunks );
			
			$this->processChunk( $immediate_chunk );
			
			foreach ( $chunks as $chunk )
			{
				$job->add_parms(
					array ( 'Caption' => get_class($this),
						    'Parameters' => join(',', $chunk) ) 
				);
			}
		}
		
		$this->logFinish();
	}
	
	function processChunk( $users )
	{
		$ldap = new LDAP();
		if ( !$ldap->connect() ) {
			$this->error("Unable connect to LDAP, abort");
			return;
		}

		$attrs = array( 'objectClass', LDAP_ATTR_DN, LDAP_ATTR_OU, LDAP_TITLE_ATTR, 
			LDAP_LOGIN_ATTR, LDAP_EMAIL_ATTR, LDAP_DESCRIPTION_ATTR );

		$values = $ldap->getNodes( LDAP_DOMAIN, $attrs );
        $dn_map = array();
        $email_map = array();

        foreach ( $values as $key => $value )
        {
            $parms = array (
                'Email' => $ldap->getAttributeValue($value, LDAP_EMAIL_ATTR),
                'Caption' => $ldap->getAttributeValue($value, LDAP_TITLE_ATTR),
                'Description' => $ldap->getAttributeValue($value, LDAP_DESCRIPTION_ATTR),
                'LDAPUID' => $value[LDAP_ATTR_DN]
            );
            $dn_map[$parms['LDAPUID']] = $parms;
            $email_map[$parms['Email']] = $parms;
        }

		$user = getFactory()->getObject('cms_User');
		$user_it = $user->getExact($users);
		
		while ( !$user_it->end() )
		{
            if ( $user_it->get('LDAPUID') == '' ) {
                // skip non-ldap users
                $user_it->moveNext();
                continue;
            }

            $parms = $dn_map[$user_it->getHtmlDecoded('LDAPUID')];
            if ( is_array($parms) ) {
                $user->modify_parms( $user_it->getId(),
                    array(
                        'Email' => $parms['Email'] != "" ? $parms['Email'] : $user_it->get('Email'),
                        'Caption' => $parms['Caption'] != "" ? $parms['Caption'] : $user_it->get('Caption'),
                        'Description' => $parms['Description'] != "" ? $parms['Description'] : $user_it->get('Description')
                    )
                );
                $this->info("Attributes have been updated using LDAPUID for: ".$user_it->getDisplayName());
            } else {
                $parms = $email_map[$user_it->getHtmlDecoded('Email')];
                if ( is_array($parms) ) {
                    $user->modify_parms( $user_it->getId(),
                        array(
                            'LDAPUID' => $parms['LDAPUID'] != "" ? $parms['LDAPUID'] : $user_it->get('LDAPUID'),
                            'Caption' => $parms['Caption'] != "" ? $parms['Caption'] : $user_it->get('Caption'),
                            'Description' => $parms['Description'] != "" ? $parms['Description'] : $user_it->get('Description')
                        )
                    );
                    $this->info("Attributes have been updated using Email for: ".$user_it->getDisplayName());
                }
            }
			$user_it->moveNext();
		}
	}

	function error( $message ) {
		try {
			$this->getLogger()->error($message);
		} catch( Exception $e ) {
		}
	}

	function info( $message ) {
		try {
			$this->getLogger()->info($message);
		} catch( Exception $e ) {
		}
	}
}
