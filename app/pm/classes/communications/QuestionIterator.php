<?php

class QuestionIterator extends StatableIterator
{
 	function get( $att )
 	{
 		if ( $att == 'Caption' )
 		{
 			return $this->getWordsOnly('Content', 10);
 		}
 		
 		return parent::get( $att );
 	}
 	
	function getAddUrl()
	{
		return $this->object->getPage().'&kind=ask';
	}
}
