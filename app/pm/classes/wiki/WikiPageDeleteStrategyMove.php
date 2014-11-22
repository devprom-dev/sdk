<?php

include_once "WikiPageDeleteStrategy.php";

class WikiPageDeleteStrategyMove extends WikiPageDeleteStrategy
{
	function deletesCascade( & $object )
	{
		if ( is_a($object, 'WikiPage') ) return false;
	}
	
	function updatesCascade( $attribute, & $self_it, & $reference_it )
	{
		return true;
		if ( $attribute == 'ParentPage' )
		{
			while( !$reference_it->end() )
			{
				$reference_it->object->modify_parms($reference_it->getId(), array (
						'ParentPage' => $self_it->get('ParentPage')
				));
				
				$reference_it->moveNext();
			}
			
			return true;
		}
	}
}