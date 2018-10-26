<?php

 ////////////////////////////////////////////////////////////////////////////////
 class ParserPageUrl
 {
 	function parse( $object_it )
 	{
 		global $model_factory, $user_it;
 		
		if( is_null($object_it) ) 
		{
			return '';
		}

		$project = $model_factory->getObject('pm_Project');
		
		$project->addFilter( new FilterAttributePredicate('VPD', $object_it->get('VPD')) );
		
		$project_it = $project->getAll();
		 
		switch ( $object_it->object->getClassName() )
		{
			case 'BlogPost':
				return 'http://devprom.ru/news/'.$object_it->getSearchName();

			case 'BlogPostTag':
			case 'Tag':
				return 'http://devprom.ru/news/tag/'.$object_it->getSearchName();
				
			case 'WikiPage':

				if ( $object_it->object instanceof ProjectPage )
				{
					$roots = $object_it->getParentsArray();
					
					$markers = array('menuitem', 'devprom.ru');
					
					$tag = $model_factory->getObject('WikiTag');
					
					$tag_it = $tag->getRegistry()->Query( array (
							new FilterAttributePredicate('Wiki', $roots)
					));
					
					$root_page = 0;
					
					while( !$tag_it->end() )
					{
						if ( in_array($tag_it->get('Caption'), $markers) )
						{ 
							$root_page = $tag_it->get('Wiki');
						}
						
						$tag_it->moveNext();
					}
					
					$tag_it =$tag->getRegistry()->Query( array (
							new FilterAttributePredicate('Wiki', $root_page)
					));
					
					$section = array_diff($tag_it->fieldToArray('Caption'), $markers);
					
					if ( count($section) < 1 ) $section[] = 'glossary';
					
					return 'http://devprom.ru/'.array_pop($section).'/'.$object_it->getSearchName();
				}
	
				if ( $object_it->object instanceof HelpPage )
				{
					return 'http://devprom.ru/docs/'.$object_it->getSearchName();
				}
				
				break;
		}
		
		return '';
 	}
 	
 	function getPhotoUrl( $user_it )
 	{
 		if ( $user_it->get('PhotoExt') != '' )
 		{
 			return '/file/photofile/'.$user_it->getId();
 		}
 		else
 		{
 			return 'http://www.gravatar.com/avatar/'.md5(trim(strtolower($user_it->get('Email')))).'?s=170';
 		}
 	}
 }
 
 ////////////////////////////////////////////////////////////////////////////////
 class SitePageUrl
 {
 	function parse( $object_it )
 	{
 		global $project_it, $user_it;
 		
		if( !is_null($object_it) ) 
		{
			$product_url = CoController::getProductUrl($project_it->get('CodeName'));
			
			switch ( $object_it->object->getClassName() )
			{
				case 'pm_Artefact':
					$url = $product_url.'download/'.$object_it->getSearchName().
						'?version='.$object_it->get('Version');
							
					if ( $object_it->IsAuthorizedDownload() && (!is_object($user_it) || !$user_it->IsReal()) )
					{
						return "javascript: authorizedDownload('".$url."')";
					}
					else
					{
						return $url;
					}

				case 'cms_User':
					return '';
					
				case 'WikiPage':
					if ( $object_it->getId() == 25701 ) {
						return 'http://devprom.ru';
					}
					break;
			}
		} 

		return ParserPageUrl::parse($object_it);
 	}
 }
