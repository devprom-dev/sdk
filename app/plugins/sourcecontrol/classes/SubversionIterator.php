<?php

class SubversionIterator extends OrderedIterator
{
 	function getDisplayName()
 	{
 		$title = $this->get('Caption');
 		
 		if ( $title != '' ) return $title;
 		
 		if ( $this->get('SVNPath') != '' ) return $this->get('SVNPath').'/'.$this->get('RootPath'); 
 		
 		return $this->get('RootPath');
 	}

     /**
      * @return SCMConnector
      */
     function getConnector()
 	{
 		$key = strtolower($this->get('ConnectorClass'));
 		
 		if ( !array_key_exists( $key, $this->object->getConnectors() ) )
 		{
 			$keys = array_keys($this->object->getConnectors());
 			$key = $keys[0];
 		}
 		
 		$part_it = getFactory()->getObject('SubversionUser')->getRegistry()->Query(
 				array (
 						new FilterAttributePredicate('Connector', $this->getId()),
 						new FilterAttributePredicate('SystemUser', getSession()->getUserIt()->getId())
 				)
 		);

 		if ( $part_it->getId() > 0 )
 		{
 		    $login_attr = 'UserName';
 		    $passwd_attr = 'UserPassword';
 		    
 			$has_credentials = $part_it->get($login_attr) != '' &&  $part_it->get($passwd_attr) != '';
 				
 			if ( $has_credentials )
 			{
		 		$credentials = new SCMCredentials( 
		 			$this->get('SVNPath'), 
		 			$this->get('RootPath'),
		 			$part_it->get($login_attr), 
		 			$part_it->get($passwd_attr) 
		 			);
 			}
 		}
 		
 		if ( !is_object($credentials) )
 		{
 			if ( $this->getId() > 0 && $this->get('LoginName') == '' )
 			{
 			}

 			if ( $this->getId() > 0 && $this->get('SVNPassword') == '' )
 			{
 			}

	 		$credentials = new SCMCredentials( 
	 			$this->get('SVNPath'), $this->get('RootPath'),
	 			$this->get('LoginName'), $this->get('SVNPassword') 
	 			);
 		}
 		
 		$connectors =& $this->object->getConnectors();
 		
 		$connectors[$key]->init( $credentials );
 		$connectors[$key]->setObject( $this->object );
 		
		return $connectors[$key]; 		
 	}
 	
  	function refresh()
 	{
 		global $model_factory;
 		
 		$log = Logger::getLogger('SCM');

 		if ( is_object($log) ) $log->info( "Refresh repository revisions: ".$this->getDisplayName() );
 		
	 	$connector = $this->getConnector();

	 	$revision = $model_factory->getObject('pm_SubversionRevision');
	 	
	 	$revision->addFilter( new FilterAttributePredicate('Repository', $this->getId()) );
	 		
 		// append newer commits of the repo
 		$last_it = $revision->getFirst( 1, 
 			array( new SortAttributeClause('CommitDate.D'),
 				   new SortAttributeClause('Version.D') ) 
 		);

 		if ( is_object($log) ) $log->info( "Latest persisted revision: ".$last_it->get("Version") );
 		
 		// get recent logs and check if they were pesrsisted already
 		$iterator = $connector->getRecentLogs( $last_it->get("Version") );

 		if ( is_object($log) ) $log->info( "There are ".$iterator->count()." more revisions in the repository" );
 		
 		$versions = array(0);
 		
 		while ( !$iterator->end() )
 		{
 			$versions[] = $iterator->get('Version'); 

 			$iterator->moveNext();
 		}
 		
 		$iterator->moveFirst();
 		
 		$stored_it = $revision->getByRefArray( array(
 			'Version' => $versions
 		));
 		
 		$stored_it->buildPositionHash( array('Version') );
 		
 		// copy revisions into devprom database
 		while ( !$iterator->end() )
 		{
 			$stored_it->moveTo('Version', $iterator->get('Version'));
 			
 			if ( !$stored_it->end() )
 			{
 				$iterator->moveNext();
 				continue;
 			}

 			$revision->add_parms( array_merge(
                 array(
                     'Project' => $this->get('Project'),
                     'Repository' => $this->getId()
                 ),
                 $connector->mapIteratorDataToDbAttributes($iterator)
            ));

 		    if ( is_object($log) ) $log->info( "Revision added: ".$iterator->get('Version') );
 			
 			$iterator->moveNext();
 		}
 		
 		return $connector;
 	}
}