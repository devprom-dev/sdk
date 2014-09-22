<?php

include "ArtefactIterator.php";
include "predicates/ArtefactVersionFilter.php";

class Artefact extends Metaobject 
{
 	function Artefact() 
 	{
		parent::Metaobject('pm_Artefact');

		$this->addAttributeGroup('IsAuthorizedDownload', 'system');
		
		$this->addAttributeGroup('IsArchived', 'system');
	}
	
	function createIterator()
	{
		return new ArtefactIterator( $this );
	}

	function getPage() 
	{
		return getSession()->getApplicationUrl().'module/fileserver/files?';
	}

	function IsDeletedCascade( $object )
	{
		return false;
	}

	function isAttributeRequired( $name ) 
	{
		if($name == 'Release' || $name == 'Build') 
		{
			return false;
		}
		
		return parent::isAttributeRequired( $name );
	}
	function getDefaultAttributeValue( $name ) 
	{
		switch( $name )
		{
		    case 'Project':
		    	return getSession()->getProjectIt()->getId();
		    	
		    case 'Participant':
		    	return getSession()->getParticipantIt()->getId();
		    
		    default:
		    	return parent::getDefaultAttributeValue($name);
		}
	}
	
	function getByKind( $kind, $is_archive )
	{
		global $model_factory;

		$sql = "SELECT t.* " .
			   "  FROM pm_Artefact t ".
			   " WHERE t.Kind = ".$kind.
			   "   AND IFNULL(t.IsArchived, 'N') = ".( $is_archive ? "'Y'" : "'N'").
			   $this->getVpdPredicate().
			   " ORDER BY t.RecordModified DESC, t.Version DESC, t.OrderNum ASC ";

		return $this->createSQLIterator($sql);
	}
	
	function getInArchive()
	{
		return $this->getByRef('IsArchived', "Y");
	}
	
	function getLatestDisplayed( $limit )
	{
		global $model_factory;
		
		$sql = " SELECT t.* FROM pm_Artefact t, pm_ArtefactType p " .
			   "  WHERE p.pm_ArtefactTypeId = t.Kind" .
			   "    AND p.IsDisplayedOnSite = 'Y' " .
			   $this->getVpdPredicate('t').
			   "    AND IFNULL(t.IsArchived, 'N') = 'N' ".
			   "  ORDER BY t.RecordModified DESC" .
			   "  LIMIT ".$limit;
			   
		return $this->createSQLIterator( $sql ); 
	}
}