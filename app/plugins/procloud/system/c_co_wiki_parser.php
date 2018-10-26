<?php

 if ( !class_exists('WikiHtmlParser', false) )
 {
 	include (SERVER_ROOT_PATH.'pm/views/wiki/parsers/WikiHtmlParser.php');
 }

 ////////////////////////////////////////////////////////////////////////////////
 class SiteWikiParser extends WikiHtmlParser
 {
 	function parse( $content = null )
 	{
 		if ( is_null($content) )
 		{
 			$object_it = $this->getObjectIt();
 			return parent::parse( $object_it->getHtmlDecoded('Content').chr(10) );
 		}
 		else
 		{
 			return parent::parse( $content );
 		}
 	}
 	
	function getPageUrl( $wiki_it ) 
	{
		global $project_it;
		
		if ( is_object($project_it) )
		{
			if ( $project_it->HasProductSite() )
			{
				$url = SitePageUrl::parse( $wiki_it );
			}
			else
			{
				$url = ParserPageUrl::parse( $wiki_it );
			}
		}
		
		if ( $url == '' )
		{
			return WikiHtmlParser::getPageUrl( $wiki_it );
		}
		
		return $url;
	}
	
	function getFileUrl( $file_it ) 
	{
		global $project_it;

		return 'http://devprom.ru/file/wikifile/'.$project_it->get('CodeName').'/'.$file_it->getId();
	}

	function hasImageTitle()
	{
		return false;
	}
 
 	function hasUrlOnImage()
	{
		return true;
	}
 }

 ////////////////////////////////////////////////////////////////////////////////
 class SiteBlogParser extends WikiHtmlParser
 {
 	function parse( $content = null )
 	{
		$this->file = getFactory()->getObject('BlogPostFile');

		if ( is_null($content) )
 		{
 			$object_it = $this->getObjectIt();
			$content = parent::parse( $object_it->getHtmlDecoded('Content').chr(10) );
			return preg_replace_callback('/\s+src="([^"]*)"/i', array($this, 'embedImages'), $content);
 		}
 		else
 		{
 			return parent::parse( $content );
 		}
 	}

	function getPageUrl( $post_it ) 
	{
		global $project_it;
		
		if ( is_object($project_it) )
		{
			if ( $project_it->HasProductSite() )
			{
				$url = SitePageUrl::parse( $post_it );
			}
			else
			{
				$url = ParserPageUrl::parse( $post_it );
			}
		}
		
		if ( $url == '' )
		{
			return WikiHtmlParser::getPageUrl( $post_it );
		}

		return $url;
	}

	function getFileUrl( $file_it ) 
	{
		global $project_it;

		return 'http://devprom.ru/file/blogfile/'.$project_it->get('CodeName').'/'.$file_it->getId();
	}

	function hasImageTitle()
	{
		return false;
	}
 
 	function hasUrlOnImage()
	{
		return false;
	}
	
	function getUidInfo( $uid )
	{
		return array();
	}

	 function embedImages( $match )
	 {
		 $url = $match[1];

		 $found = array();
		 if ( !preg_match('/file\/([^\/]+)\/([^\/]+)\/([\d]+).*/', $url, $found) ) {
			 if ( !preg_match('/file\/([^\/]+)\/([\d]+).*/', $url, $found) ) return $match[0];
			 $file_class = $found[1];
			 $file_id = $found[2];
		 } else {
			 $file_class = $found[1];
			 $file_id = $found[3];
		 }
		 if ( $file_class != 'blogfile' ) return $match[0];

		 $file_it = $this->file->getExact($file_id);
		 if ( $file_it->getId() == '' ) return $match[0];

		 $finfo = new \finfo(FILEINFO_MIME_TYPE);
		 $path = $file_it->getFilePath('Content');
		 return ' src="data:'.$finfo->file($path).';base64,'.base64_encode(file_get_contents($path)).'"';
	 }
 }
 