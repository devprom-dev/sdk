<?php
include_once SERVER_ROOT_PATH."core/classes/export/IteratorExport.php";

class RequestIteratorExportBlog extends IteratorExport
{
	function export()
	{
 		global $project_it, $model_factory;
 		
 		$blog_post = $model_factory->getObject('BlogPost');

 		$hashids = $model_factory->getObject('HashIds');

 		$hash = $hashids->getHash( $this );
 		
		exit(header('Location: '.$blog_post->getPageNameObject().'&from=requests&items='.$hash));
 	}
}
