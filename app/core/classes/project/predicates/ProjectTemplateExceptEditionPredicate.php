<?php

class ProjectTemplateExceptEditionPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		return " AND IFNULL(t.ProductEdition,'custom') NOT IN('".(join("','",preg_split('/,/', $filter)))."') ";
 	}
}
