<?php

class SortProjectTemplateLanguageClause extends SortClauseBase
{
 	var $language;
 	
 	function SortProjectTemplateLanguageClause ( $language )
 	{
 		$this->language = $language;
 		parent::SortClauseBase();
 	}
 	
 	function clause()
 	{
 		return "CASE Language WHEN ".$this->language." THEN 0 ELSE Language END ASC";
 	}
}
