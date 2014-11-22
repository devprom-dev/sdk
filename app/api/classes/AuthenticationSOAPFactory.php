<?php

class AuthenticationSOAPFactory extends AuthenticationFactory
{
    var $use_it;
    
 	function getToken()
 	{
        $user_it = $this->getUser();
        
    	return $user_it->getApiKey();
 	}
 	
 	function ready()
 	{
 	    return $_REQUEST['token'] != '';
 	}
    
 	function tokenRequired()
 	{
 	    return true;
 	}
 	
 	function credentialsRequired()
 	{
 	    return true;
 	}
 	
    function login( $codename = '' )
    {
        $user_it = $this->getUser();
        
    	$hash = $this->getToken();
	
	    $systemuse = getFactory()->getObject('pm_ProjectUse');
	    
		$systemuse->defaultsort = 'RecordModified DESC';

    	$this->use_it = $systemuse->getByRefArray(array('SessionHash' => $hash), 1);
    	
    	if ( $this->use_it->count() < 1 )
    	{
    		$id = $systemuse->add_parms( 
    			array( 'SessionHash' => $hash,
    				   'Participant' => $user_it->getId() ) 
    			);
    			
    		$this->use_it = $systemuse->getExact( $id );
    	}
    	
		$project = getFactory()->getObject('pm_Project');
		
		$project_it = $project->getByRef('LCASE(CodeName)', strtolower($codename));
    		
		if ( $project_it->getId() > 0 )
		{
			$systemuse->getRegistry()->Store( $this->use_it,
					array( 
					        'Project' => $project_it->getId(),
					        'Participant' => $user_it->getId() 
					)
			);
		}
		else
		{
			$systemuse->getRegistry()->Store( $this->use_it,
					array( 
					        'Project' => '',
					        'Participant' => $user_it->getId()
					)
			);
		}
		
   	    $this->use_it = $systemuse->getExact($this->use_it->getId());
    }
        
 	function authorize()
 	{
 	    global $model_factory;
 	    
		$user = $model_factory->getObject('cms_User');
 	    
		$systemuse = $model_factory->getObject('pm_ProjectUse');
		
		$systemuse->defaultsort = 'RecordModified DESC';
		
		$this->use_it = $systemuse->createCachedIterator(array());
		 
		$token = $_REQUEST['token'];

 		if ( $token == '' )
		{
		    return $user->createCachedIterator(array());
		}
 	    
		$this->use_it = $systemuse->getByRefArray( array('SessionHash' => $token), 1 );
			
		if ( $this->use_it->count() < 1 )
		{
		    return $user->createCachedIterator(array());
		}

 		if ( $this->use_it->get('Participant') < 1 )
		{
		    return $user->createCachedIterator(array());
		}
		
		$user_it = $user->getExact( $this->use_it->get('Participant') );

		if ( $user_it->getId() < 1 )
		{
		    return $user->createCachedIterator(array());
		}            		
 	    
		$this->setUser($user_it);
		
		return $user_it;
 	}

    function getProject()
    {
        if ( !is_object($this->use_it) )
        {
            $this->authorize();
        }
        
        return $this->use_it->get('Project');
    }
    
    function getAuthData()
    {
    	return array($this->use_it->getId(), $this->use_it->get('Participant'));
    }
}
