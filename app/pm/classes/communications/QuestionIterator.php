<?php

class QuestionIterator extends StatableIterator
{
 	function get( $att )
 	{
 		if ( $att == 'Caption' ) {
            $html2text = new \Html2Text\Html2Text(parent::getHtmlDecoded('Content'), array('width'=>0));
 			return $this->getWordsOnlyValue(preg_replace('/[r\n]+/', ' ', $html2text->getText()), 10);
 		}
 		
 		return parent::get( $att );
 	}
 	
	function getAddUrl()
	{
		return $this->object->getPage().'&kind=ask';
	}
}
