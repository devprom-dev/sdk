<?php

class QuestionIterator extends StatableIterator
{
 	function get( $att )
 	{
 		if ( $att == 'Caption' ) {
            return $this->object->getDisplayName();
 		}
 		
 		return parent::get( $att );
 	}
 	
	function getAddUrl()
	{
		return $this->object->getPage().'&kind=ask';
	}
}
