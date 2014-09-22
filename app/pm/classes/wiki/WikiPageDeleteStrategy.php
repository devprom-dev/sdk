<?php

abstract class WikiPageDeleteStrategy
{
	abstract function deletesCascade( & $object );
	
	abstract function updatesCascade( $attribute, & $self_it, & $reference_it );
}