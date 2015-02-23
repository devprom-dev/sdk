<?php

include_once "ModelDataTypeMapping.php";

class ModelDataTypeMappingTag extends ModelDataTypeMapping
{
	public function applicable( $type_name )
	{
		return strpos($type_name, 'ref_tagid') !== false;
	}
	
	public function map( $value )
	{
		$tag = getFactory()->getObject('Tag');
		
		$tag_it = $tag->getExact($value);
		
		if ( $tag_it->getId() < 1 )	{
			$tag_it = $tag->getByRef('Caption', $value);
		}
	
		return $tag_it->getId() > 0 ? $tag_it->getId() : $tag->add_parms( array('Caption' => $value) ) ;
	}
}