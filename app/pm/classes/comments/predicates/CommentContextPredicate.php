<?php

class CommentContextPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$user_it = getSession()->getUserIt();
 		
 		switch ( $filter )
 		{
 			case 'my':
 				return " AND t.AuthorId = ".$user_it->getId();
 				
 			case 'needreply':
 				return " AND t.RecordModified = " .
 					   "		(SELECT IFNULL(MAX(c2.RecordModified), t.RecordModified) " .
 					   " 		   FROM Comment c2 " .
 					   "  		  WHERE c2.ObjectId = t.ObjectId " .
 					   "		    AND c2.ObjectClass = t.ObjectClass )" .
 					   " AND t.AuthorId <> ".$user_it->getId();
 		}
 	}
} 
