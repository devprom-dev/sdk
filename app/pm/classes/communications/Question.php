<?php

include "QuestionIterator.php";
include "predicates/QuestionAuthorFilter.php";
include "persisters/QuestionLastCommentPersister.php";
include "persisters/QuestionRequestPersister.php";
include SERVER_ROOT_PATH."pm/classes/tags/persisters/QuestionTagPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/attachments/persisters/AttachmentsPersister.php";

class Question extends MetaobjectStatable
{
 	function __construct()
 	{
 	    global $model_factory;
 	    
 		parent::__construct('pm_Question');
 		
		$this->addAttribute( 'Owner', 'REF_pm_ParticipantId', translate('Ответственный'), true, true );
			
 		$this->addAttribute('Comments', '', translate('Комментарии'), true);
		
		$this->addAttribute('Attachment', 'REF_pm_AttachmentId', translate('Приложения'), true);
 		
		$this->addAttribute('TraceRequests', 'REF_pm_ChangeRequestId', translate('Пожелания'), true );
		
 		$this->addPersister( new QuestionRequestPersister() );
		
 		$tag = $model_factory->getObject('CustomTag');
		
 		$this->addAttribute( 'Tags', 'REF_TagId', translate('Тэги'), false, false, '', 40 );
 		
 		$this->addPersister( new QuestionTagPersister() );
 		
		$this->addAttribute('LastCommentDate', 'DATETIME', text(1201), false, false, '', 1);

		$this->addPersister( new QuestionLastCommentPersister() );
		
		$this->addPersister( new AttachmentsPersister() );
 	}
 	
	function createIterator() 
	{
		return new QuestionIterator($this);
	}

	function getPage() 
	{
	    $session = getSession();
	    
		return $session->getApplicationUrl().'project/question?';
	}
	
	function getDefaultAttributeValue( $attribute )
	{
		if ( $attribute == 'Author' )
		{
			return getSession()->getUserIt()->getId();
		}
		
		return parent::getDefaultAttributeValue( $attribute );
	}
	
	function getWithoutAnswersIt( $days )
	{
		global $model_factory;
		
		$sql = " SELECT t.* " .
			   "   FROM pm_Question t " .
			   "  WHERE (SELECT COUNT(1) " .
 			   " 	  	   FROM Comment c " .
 			   "	      WHERE c.ObjectId = t.pm_QuestionId " .
 			   "	        AND c.ObjectClass = 'question' ) = 0 " .
 			   "    AND TO_DAYS(NOW()) - TO_DAYS(RecordModified) < '".$days."'" .
 			   $this->getVpdPredicate('t').$this->getFilterPredicate('t').
 			   "  ORDER BY t.RecordModified DESC ";
		 
		return $this->createSQLIterator( $sql );
	}
}