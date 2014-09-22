<?php

class TagBlogPostPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array( 
 			" ( SELECT GROUP_CONCAT(CAST(rt.BlogPost as CHAR)) ".
 			"	  FROM BlogPostTag rt " .
			"	 WHERE rt.Tag = " .$this->getPK($alias)." ) BlogPosts " 
 		);
 	}
}
