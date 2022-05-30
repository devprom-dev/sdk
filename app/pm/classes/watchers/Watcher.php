<?php
include "WatcherIterator.php";
include "WatcherRegistry.php";
include "validators/ModelValidatorWatcherSubject.php";
include_once "persisters/WatchersPersister.php";
include_once "predicates/WatcherUserPredicate.php";

class Watcher extends Metaobject
{
 	var $object_it, $size;
 	
 	function Watcher( $object_it = null, ObjectRegistry $registry = null )
 	{
 		parent::__construct('pm_Watcher', is_object($registry) ? $registry : new WatcherRegistry($this));
 		
 		$this->setAttributeType('SystemUser', 'REF_IssueAuthorId');
        $this->addAttributeGroup('SystemUser', 'skip-mapper');

		$this->object_it = $object_it;
 	}
 	
 	function createIterator() {
 		return new WatcherIterator( $this );
 	}
 	
 	function getObjectIt() {
 		return $this->object_it;
 	}

    function getAnchorIt() {
        return $this->object_it;
    }

    function getAll()
 	{
 		if ( !is_object($this->object_it) ) {
 			return $this->getByRef('ObjectId', "-1");
 		}
 		return parent::getAll();
 	}
 	
 	function getWatched( $user_it )
 	{
 		return $this->getByRefArray(
 			array ( 
 				'SystemUser' => $user_it->getId()
 				)
 			);
 	}
 	
 	function watchedByEmail( $email )
 	{
 		return $this->getByRefArray(
 			array ( 
 				'LCASE(Email)' => strtolower($email)
 				)
 			);
 	}

 	function getAllWatched( $user_it )
 	{
 		return $this->getByRefArray(
 			array (	'SystemUser' => $user_it->getId() )
 			);
 	}

	function add_parms( $parms )
	{
	    if ( is_numeric($parms['SystemUser']) && $parms['SystemUser'] > 1000000 ) {
	        $userIt = getFactory()->getObject('IssueAuthor')->getExact($parms['SystemUser']);
	        $parms['Email'] = $userIt->get('Email');
        }

		if ( $parms['Email'] == '' && !is_numeric($parms['SystemUser']) ) {
			$parms['Email'] = $parms['SystemUser'];
			unset($parms['SystemUser']);
		}

	    if ( is_object($this->object_it) )
	    {
	        if ( !array_key_exists('ObjectId', $parms) ) {
	            $parms['ObjectId'] = $this->object_it->getId();
	        }
	        if ( !array_key_exists('ObjectClass', $parms) ) {
	            $parms['ObjectClass'] = strtolower(get_class($this->object_it->object));
	        }
	    }
	    
	    return parent::add_parms( $parms );
	}

	function getValidators()
    {
        return array_merge(
            parent::getValidators(),
            array(
                new ModelValidatorWatcherSubject()
            )
        );
    }
}