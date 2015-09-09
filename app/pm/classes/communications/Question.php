<?php

include "QuestionIterator.php";
include "predicates/QuestionAuthorFilter.php";

class Question extends MetaobjectStatable
{
 	function __construct()
 	{
 	    global $model_factory;

 		parent::__construct('pm_Question');


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