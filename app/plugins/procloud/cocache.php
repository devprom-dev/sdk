<?php

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ( $_REQUEST['mode'] == 'style' )
{
	$expires = 60 * 60 * 24 * 3;
	
 	header("Pragma: public");
 	header("Cache-Control: maxage=". $expires);
 	header("Expires: " . gmdate('D, d M Y H:i:s', time()+$expires) . " GMT");
	//header("Last-Modified: " . $artefact_it->getDateFormatUser("RecordModified", "%a, %d %b %Y %H:%I:%S"). " GMT");
	header("Content-type: text/css");
	
	$filepath = dirname(__FILE__).'/style.css';
	
	$file = fopen( $filepath, "r" );
	echo fread( $file, filesize($filepath));

	$filepath = dirname(__FILE__).'/stylex.css';
	
	$file = fopen( $filepath, "r" );
	echo fread( $file, filesize($filepath));

	$filepath = dirname(__FILE__).'/../../styles/fancybox/fancy.css';
	
	$file = fopen( $filepath, "r" );
	echo fread( $file, filesize($filepath));

	die();	
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ( $_REQUEST['mode'] == 'stylex' )
{
	$expires = 60 * 60 * 24 * 3;
	
 	header("Pragma: public");
 	header("Cache-Control: maxage=". $expires);
 	header("Expires: " . gmdate('D, d M Y H:i:s', time()+$expires) . " GMT");
	//header("Last-Modified: " . $artefact_it->getDateFormatUser("RecordModified", "%a, %d %b %Y %H:%I:%S"). " GMT");
	header("Content-type: text/css");
	
	$filepath = dirname(__FILE__).'/stylex.css';
	
	$file = fopen( $filepath, "r" );
	echo fread( $file, filesize($filepath));

	$filepath = dirname(__FILE__).'/../../styles/fancybox/fancy.css';

	$file = fopen( $filepath, "r" );
	echo fread( $file, filesize($filepath));

	die();	
}

if ( $_REQUEST['mode'] == 'image' )
{
	$expires = 60 * 60 * 24 * 3;
	
 	header("Pragma: public");
 	header("Cache-Control: maxage=". $expires);
 	header("Expires: " . gmdate('D, d M Y H:i:s', time()+$expires) . " GMT");
	header("Content-type: image/png");


	$filepath = dirname(__FILE__).'/_images/'.basename($_REQUEST['image']);
	if ( !file_exists($filepath) )
	{
		$filepath = dirname(__FILE__).'/../../images/'.basename($_REQUEST['image']);
	}

	$file = fopen( $filepath, "r" );
	echo fread( $file, filesize($filepath));

	die();	
}

$files = array('cms_user','blogfile', 'wikifile', 'photofile', 'cms_tempfile', 'blogpostfile', 'wikipagefile', 'pm_attachment', 'pm_artefact');

if ( in_array(strtolower($_REQUEST['mode']), $files) )
{
	include ('common.php');

	$expires = 60 * 60 * 24 * 3;
	
	switch ( strtolower($_REQUEST['mode']) )
	{
		case 'blogfile':
		case 'blogpostfile':
 			$object = $model_factory->getObject2('metaobject', 'BlogPostFile');
 			$attribute = 'Content';
 			break;
 				
		case 'wikifile':
		case 'wikipagefile':
 			$object = $model_factory->getObject2('metaobject', 'WikiPageFile');
 			$attribute = 'Content';
 			break;
	
		case 'photofile':
		case 'cms_user':
 			$object = $model_factory->getObject('cms_User');
 			$attribute = 'Photo';
 			break;

		case 'cms_tempfile':
 			$object = $model_factory->getObject('cms_TempFile');
 			$attribute = 'File';
 			break;

		case 'pm_attachment':
 			$object = $model_factory->getObject('pm_Attachment');
 			$attribute = 'File';
 			break;

		case 'pm_artefact':
 			$object = $model_factory->getObject('pm_Artefact');
 			$attribute = 'Content';
 			break;
	}
	
	list($file_id, $file_ext) = preg_split('/\./', $_REQUEST['file']);
	
 	$it = $object->getExact( $file_id );
 	
	if ( $it->count() < 1 )
	{
		exit(header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"));
	}
	
	if ( $it->get('ReferenceName') == '4' && (!is_object($user_it) || is_object($project_it) && !$project_it->IsUserParticipate( $user_it->getId())) && $it->get('WikiPage') > 0 && $project_it->HasProductSite() )
	{
		$page_it = $it->getRef('WikiPage');
		$parent_it = $page_it->getRef('ParentPage');

		if ( $parent_it->count() < 1 )
		{
			exit(header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"));
		}

		$tag_it = $parent_it->getTagsIt();

		$public = false;
		while ( !$tag_it->end() )
		{
			if ( $tag_it->getDisplayName() == 'public' || $tag_it->getDisplayName() == 'sitepage' )
			{
				$public = true;
				break;
			}
			$tag_it->moveNext();
		}
        
		if ( !$public )
		{
			exit(header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"));
		}
	}

	$file_path = SERVER_FILES_PATH.$object->getClassName().'/'.basename($it->getFilePath( $attribute ));

 	header("Pragma: public");
 	header("Cache-Control: maxage=". $expires);
 	header("Expires: " . gmdate('D, d M Y H:i:s', time()+$expires) . " GMT");
	header("Content-type: image/png");


	if ( file_exists($file_path) )
	{
	 	$downloader = new Downloader;
	
	  	$downloader->echoFile($file_path, $it->getFileName( $attribute ), 
	  		$it->getFileMime( $attribute ));
	}
	else
	{
		exit(header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"));
	}
 		
 	die();
}

?>