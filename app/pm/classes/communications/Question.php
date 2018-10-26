<?php
include "QuestionIterator.php";
include "predicates/QuestionAuthorFilter.php";

class Question extends MetaobjectStatable
{
 	function __construct() {
 		parent::__construct('pm_Question');
 	}

	function createIterator() {
		return new QuestionIterator($this);
	}

	function getPage() {
		return getSession()->getApplicationUrl().'project/question?';
	}
	
	function getDefaultAttributeValue( $attribute )
	{
		if ( $attribute == 'Author' ) {
			return getSession()->getUserIt()->getId();
		}
		return parent::getDefaultAttributeValue( $attribute );
	}

	function getDisplayName()
    {
        return translate('Обсуждение');
    }
}