<?php

include "SubversionRevisionIterator.php";
include "predicates/SubversionRevisionRequirementPredicate.php";
include "persisters/SourceCodeRequestPersister.php";
include "persisters/SourceCodeTaskPersister.php";
include "persisters/SourceCodeParticipantPersister.php";
        
class SubversionRevision extends Metaobject
{
 	var $parts_cache;
 	
 	function __construct() 
 	{
 		parent::__construct('pm_SubversionRevision');
 		
 		$this->addAttribute( 'Issues', 'REF_RequestId', translate('Пожелания'), false);
 		
 		$this->addPersister( new SourceCodeRequestPersister() );
 		
 		$this->addAttribute( 'Tasks', 'REF_TaskId', translate('Задачи'), false);
 		
 		$this->addPersister( new SourceCodeTaskPersister() );

 		$this->addAttribute( 'Participant', 'REF_ParticipantId', translate('Автор'), false);
 		
 		$this->addPersister( new SourceCodeParticipantPersister() );
 		
		foreach ( array('Version', 'Description', 'Author', 'CommitDate') as $attribute )
		{
        	$this->addAttributeGroup($attribute, 'tooltip');
		}
 	}
 	
 	function createIterator() 
 	{
 		return new SubversionRevisionIterator( $this );
 	}
 	
 	function getLike( $text )
 	{
 		return parent::getLike( $text, 'Description' );
 	}
 	
 	function getPage()
 	{
 	    return getSession()->getApplicationUrl().'module/sourcecontrol/revision?mode=details&'; 
 	}
}