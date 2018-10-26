<?php

 if ( !class_exists('BlogPost') )
 {
 	include( SERVER_ROOT_PATH.'pm/classes/communications/PMBlogPost.php' );
 }
 
 //////////////////////////////////////////////////////////////////////////////
 class ProCloudBlogPost extends BlogPost
 {
 }
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class PublicBlogPostFilter extends FilterPredicate
 {
 	function _predicate( $filter )
 	{
		return " AND t.VPD IN (SELECT i.VPD FROM pm_PublicInfo i, pm_Project p" .
				   "  			WHERE i.Project = p.pm_ProjectId" .
				   "    		  AND i.IsProjectInfo = 'Y' " .
				   "    		  AND i.IsBlog = 'Y' " .
				   "    		  AND p.CodeName NOT IN ('procloud') ) ";
 	}
 }
 
?>