<?php
 $path = dirname(dirname(__FILE__));
 
 require_once($path.'/../../pm/views/wiki/parsers/WikiParser.php');
 require_once(dirname(__FILE__).'/../system/c_co_url.php');
 require_once(dirname(__FILE__).'/../system/c_co_wiki_parser.php');
 
 ////////////////////////////////////////////////////////////////////////////
 class Atom extends Command
 {
 	function execute()
	{
		global $_REQUEST, $model_factory, $project, $project_it, $user_it;

		switch ( strtolower($_REQUEST['entity']) )
		{
			case 'project':
				$vacancy = $model_factory->getObject('pm_Project');
				$vacancy_it = $vacancy->getLatestPublicIt(20);
				
				$title = translate('Новые публичные проекты');
				$id = 'devprom.projects: '.$vacancy_it->getId();
				$url = CoController::getCatalogueUrl();
				
				$this->replyAtom( $title, $id, $url, $vacancy_it );
				break;

			case 'dblog':
			    $model_factory->enableVpd(false);
			    
				$project = $model_factory->getObject('pm_Project');
				$project_it = $project->getByRef("CodeName", 'procloud');
				
				$post = $model_factory->getObject('BlogPost');
				$post_it = $post->getByRefArray(
					array( 'Blog' => $project_it->get('Blog') ), 10);
					
				$title = 'Облако проектов: '.translate('Блог сервиса');
				$id = 'devprom.blog: '.$post_it->getId();
				$url = '/blog/';
				
				$this->replyAtom( $title, $id, $url, $post_it );
			    
			    $model_factory->enableVpd(true);
				break;

			case 'blog':
			    $model_factory->enableVpd(false);
			    
				$project = $model_factory->getObject('pm_Project');
				$project_it = $project->getByRef("LCASE(t.CodeName)", strtolower($_REQUEST['project']));

				if ( $project_it->IsPublic() && $project_it->IsPublicBlog() )
				{
					$post = $model_factory->getObject('BlogPost');
					$post_it = $post->getByRefArray(
						array( 'Blog' => $project_it->get('Blog') ), 10);
						
					$title = $project_it->getDisplayName().': '.translate('Блог проекта');
					$id = $project_it->get('CodeName').'.blog: '.$post_it->getId();
					$url = '/news/'.$project_it->get('CodeName');
					
					$this->replyAtom( $title, $id, $url, $post_it );
				}
			    
			    $model_factory->enableVpd(true);
				break;

			case 'news':
				$post = $model_factory->getObject('procloud.BlogPost');
				$post->disableVpd();
				
				$post->addFilter( new PublicBlogPostFilter('-') );
				$post_it = $post->getLatest( 10 );

				$title = text('procloud299');
				$id = 'procloud.news: '.$post_it->getId();
				
				$this->replyAtom( $title, $id, '/news', $post_it );
				break;
		}
	}
	
	function replyAtom( $title, $id, $url, $object_it )
	{
		global $_REQUEST, $project, $project_it;
		
		$configuration = getConfiguration();
		
		$xml = '<?xml version="1.0" encoding="windows-1251"?>' .
				'<feed xmlns="http://www.w3.org/2005/Atom">'.Chr(10);
		
		$xml .= '<link rel="alternate" type="text/html" hreflang="en" href="'._getServerUrl().htmlspecialchars($url).'"/>'.Chr(10);
		$xml .= '<link rel="self" type="application/atom+xml" href="'.
			urlencode(_getServerUrl().'/core/command.php?class=atom&entity='.$_REQUEST['entity']).'"/>'.Chr(10);

		$common = array (
			'title' => $title,
			'subtitle' => '',
			'updated' => $object_it->getDateFormatUser('RecordCreated', '%Y-%m-%dT%H:%I:%SZ'),
			'id' => $id
			);
		
		$xml .= $this->convert($common);
		
		$xml .= '<generator uri="'._getServerUrl().'" version="1.0">Облако проектов</generator>'.Chr(10);
		$xml .= '<author><name>Облако проектов</name></author>'.Chr(10);
		$xml .= '<rights>Copyright (c) '.date('Y', time()).' Облако проектов</rights>'.Chr(10);
		
		while ( !$object_it->end() )
		{
			if ( $object_it->object->getClassName() == 'BlogPost' )
			{
				$parser = new SiteBlogParser($object_it);	
				$content = $parser->parse();
			}
			else
			{
				$content = $object_it->get('Description');
			}
			
			$xml .= '<entry>'.Chr(10);

			$entry = array (
				'title' => '<![CDATA['.$object_it->getDisplayName().']]>',
				'id' => 'devprom.'.$_REQUEST['entity'].': '.$object_it->getId(),
				'updated' => $object_it->getDateFormatUser('RecordCreated', '%Y-%m-%dT%H:%I:%SZ'),
				'content' => htmlspecialchars(str_replace(chr(10), '<br/>', $content))
				);
			
			$xml .= $this->convert($entry);
			
			if ( $object_it->object->getClassName() != 'BlogPost' )
			{
				$object_url = _getServerUrl().$object_it->getViewUrl();
			}
			else
			{
				if ( $url == 'news' || $url == '/news' )
				{
					$object_url = ParserPageUrl::parse($object_it);
				}
				else
				{
					$object_url = _getServerUrl().$url.'/'.$object_it->getId();
				}
			}
			
			$xml .= '<author><name>Облако проектов</name></author>'.Chr(10);
			$xml .= '<rights>Copyright (c) '.date('Y', time()).' Облако проектов</rights>'.Chr(10);
			$xml .= '<link rel="alternate" type="text/html" href="'.htmlspecialchars($object_url).'"/>'.Chr(10);
			$xml .= '</entry>'.Chr(10);
			
			$object_it->moveNext();
		}
		
		$xml .= '</feed>';
		
		header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		header('Content-type: text/xml; charset=windows-1251');
		
		echo $xml;
	}

 	function convert ( $attributes )
 	{
		$tags = array_keys($attributes);
		
		for ( $i = 0; $i < count($tags); $i++ )
		{
			if ( $tags[$i] == 'content' )
			{
				$result .= '<'.$tags[$i].' type="html">'.$attributes[$tags[$i]].'</'.$tags[$i].'>'.Chr(10);
			}
			else
			{
				$result .= '<'.$tags[$i].'>'.$attributes[$tags[$i]].'</'.$tags[$i].'>'.Chr(10);
			}
		}
		
		return $result;
 	}
 }
 
?>