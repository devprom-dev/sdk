<?php

class LanguageEntityIterator extends OrderedIterator
{
 	function get( $attr )
 	{
 		switch( $attr )
 		{
 			case 'Caption':
 				return translate(parent::get($attr));
 				
 			default:
 				return parent::get($attr);
 		}
 	}
}
