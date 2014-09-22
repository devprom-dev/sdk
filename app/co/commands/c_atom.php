<?php

 ////////////////////////////////////////////////////////////////////////////
 class Atom extends Command
 {
 	function execute()
	{
		global $_REQUEST, $model_factory, $project, $project_it;

		$user_it = getSession()->getUserIt();
		
		switch ( strtolower($_REQUEST['entity']) )
		{
			case 'news':
				$user = $model_factory->getObject('cms_User');
				$user_it = $user->getAll();
				$authorizedaccess = false;
				
				while ( !$user_it->end() )
				{
					if ( $_REQUEST['project'] == $user_it->getNewsKey() )
					{
						$authorizedaccess = true;
						break;
					}
					
					$user_it->moveNext();
				}
				
				if ( !$authorizedaccess )
				{
					break;
				}

				$post = $model_factory->getObject('BlogPost');
				$post->disableVpd();
				
				$post->addFilter( new NewsBlogPostFilter('-') );
				$post_it = $post->getLatest(10);

				$title = text(299);
				$id = 'devprom.news: '.$post_it->getId();
				
				$this->replyAtom( $title, $id, '/news', $post_it );
				break;
		}
	}
	
	function replyAtom( $title, $id, $url, $object_it )
	{
		global $_REQUEST, $model_factory, $project_it;
		
		$project = $model_factory->getObject('pm_Project');
		
		$project->addFilter( new FilterAttributePredicate('VPD', $object_it->get('VPD')) );
		
		$project_it = $project->getAll(); 
		
		$xml = '<?xml version="1.0" encoding="windows-1251"?>' .
				'<feed xmlns="http://www.w3.org/2005/Atom">'.Chr(10);
		
		$xml .= '<link rel="alternate" type="text/html" hreflang="en" href="'._getServerUrl().htmlspecialchars($url, ENT_COMPAT | ENT_HTML401, 'cp1251').'"/>'.Chr(10);
		$xml .= '<link rel="self" type="application/atom+xml" href="'.
			urlencode(_getServerUrl().'/core/command.php?class=atom&entity='.SanitizeUrl::parseUrl($_REQUEST['entity'])).'"/>'.Chr(10);

		$common = array (
			'title' => $title,
			'subtitle' => '',
			'updated' => $object_it->getDateFormatUser('RecordCreated', '%Y-%m-%dT%H:%I:%SZ'),
			'id' => $id
			);
		
		$xml .= $this->convert($common);
		
		$xml .= '<generator uri="'._getServerUrl().'" version="1.0">DEVPROM</generator>'.Chr(10);
		$xml .= '<author><name>DEVPROM</name></author>'.Chr(10);
		$xml .= '<rights>Copyright (c) '.date('Y', time()).' DEVPROM</rights>'.Chr(10);
		
		while ( !$object_it->end() )
		{
			if ( $object_it->object->getClassName() == 'BlogPost' )
			{
				$class = $object_it->get('ContentEditor');
				if ( class_exists($class) )
				{
					$editor = new $class;
					$editor->setObjectIt( $object_it );
					
					$parser = $editor->getHtmlParser();
					$parser->setObjectIt( $object_it );
	
					$content = $parser->parse( $object_it->get('Content') );
				}
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
				'content' => htmlspecialchars(str_replace(chr(10), '<br/>', $content), ENT_COMPAT | ENT_HTML401, 'cp1251')
				);
			
			$xml .= $this->convert($entry);
			
			$uid = new ObjectUID;
			$object_url = _getServerUrl().$uid->getGotoUrl($object_it);
			
			$xml .= '<author><name>DEVPROM</name></author>'.Chr(10);
			$xml .= '<rights>Copyright (c) '.date('Y', time()).' DEVPROM</rights>'.Chr(10);
			$xml .= '<link rel="alternate" type="text/html" href="'.htmlspecialchars($object_url, ENT_COMPAT | ENT_HTML401, 'cp1251').'"/>'.Chr(10);
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