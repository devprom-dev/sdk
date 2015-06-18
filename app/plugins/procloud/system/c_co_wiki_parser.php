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
		return true;
	}
	
	function getUidInfo( $uid )
	{
		return array();
	}
 }
 