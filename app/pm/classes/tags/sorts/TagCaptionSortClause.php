<?php

class TagCaptionSortClause extends SortAttributeClause
{
 	function __construct() {
 		parent::__construct('Caption');
 	}
 	
 	function clause()
 	{
        $ref_field = $this->getAlias() != '' ? $this->getAlias().'.Tag' : 'Tag';
		return " (SELECT s.Caption FROM Tag s WHERE s.TagId = ".$ref_field.") ".$this->getSortType()." ";
 	}
}