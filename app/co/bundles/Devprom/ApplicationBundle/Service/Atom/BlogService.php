<?php

namespace Devprom\ApplicationBundle\Service\Atom;

include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';

class BlogService
{
	function replyAtom( $key )
	{
		$project_it = $this->getProjectIt( $key );
		
		$object_it = $this->getObjectIt($project_it);
		
		$xml = '<?xml version="1.0" encoding="'.APP_ENCODING.'"?>' .
				'<feed xmlns="http://www.w3.org/2005/Atom">'.Chr(10);
		
		$xml .= '<link rel="alternate" type="text/html" hreflang="en"/>'.Chr(10);
		$xml .= '<link rel="self" type="application/atom+xml"/>'.Chr(10);

		$common = array (
			'title' => $this->getTitle(),
			'subtitle' => '',
			'updated' => $object_it->getDateFormatUser('RecordCreated', '%Y-%m-%dT%H:%I:%SZ'),
			'id' => $this->getBlogId($project_it)
			);
		
		$xml .= $this->convert($common);
		
		$xml .= '<generator uri="'._getServerUrl().'" version="1.0">DEVPROM</generator>'.Chr(10);
		$xml .= '<author><name>DEVPROM</name></author>'.Chr(10);
		$xml .= '<rights>Copyright (c) '.date('Y', time()).' DEVPROM</rights>'.Chr(10);
		
		while ( !$object_it->end() )
		{
			$copy_it = $object_it->copy();
			$editor = \WikiEditorBuilder::build($object_it->get('ContentEditor'));
			$editor->setObjectIt($copy_it);
			
			$parser = $editor->getHtmlParser();
			$parser->setObjectIt($copy_it);

		 	$parser->setRequiredExternalAccess();
			$content = $parser->parse( $object_it->getHtmlDecoded('Content') );
			
			$xml .= '<entry>'.Chr(10);
			$entry = array (
				'title' => '<![CDATA['.$object_it->get('Caption').']]>',
				'id' => 'devprom.post: '.$object_it->getId(),
				'updated' => $object_it->getDateFormatUser('RecordCreated', '%Y-%m-%dT%H:%I:%SZ'),
				'content' => htmlspecialchars($content, ENT_COMPAT | ENT_HTML401, APP_ENCODING)
				);
			$xml .= $this->convert($entry);
			
			$xml .= '<author><name>Devprom.ALM</name></author>'.Chr(10);
			$xml .= '<rights>Copyright (c) '.date('Y', time()).' Devprom</rights>'.Chr(10);
			$xml .= '</entry>'.Chr(10);
			
			$object_it->moveNext();
		}
		
		$xml .= '</feed>';
		
		return $xml;
	}

	public function getUrl( $project_it )
	{
		return _getServerUrl().htmlspecialchars('/news/'.$this->getKey($project_it), ENT_COMPAT | ENT_HTML401, APP_ENCODING);
	}
	
	private function getKey( $project_it )
	{
		return md5($project_it->get('CodeName').\EnvironmentSettings::getServerSalt());
	}
	
	private function getProjectIt( $key )
	{
		$project_it = getFactory()->getObject('Project')->getRegistry()->getAll();
		
		while( !$project_it->end() )
		{
			if ( $this->getKey($project_it) == $key )
			{
				return $project_it->copy();
			}
			
			$project_it->moveNext();
		}
		
		$portfolio_it = getFactory()->getObject('Portfolio')->getAll();

		while( !$portfolio_it->end() )
		{
			if ( $this->getKey($portfolio_it) == $key )
			{
				return $portfolio_it->copy();
			}
			
			$portfolio_it->moveNext();
		}
		
		return getFactory()->getObject('Project')->getEmptyIterator();
	}
	
	private function getObjectIt( $project_it )
	{
		if ( $project_it->getId() < 1 )
		{
			return getFactory()->getObject('BlogPost')->getEmptyIterator();
		}
		
		return getFactory()->getObject('BlogPost')->getRegistry()->Query(
				array(
						new \FilterVpdPredicate(
								array_merge( 
										$project_it->getRef('LinkedProject')->fieldToArray('VPD'),
										array($project_it->get('VPD'))
								)
						),
						new \SortRecentClause(),
						new \EntityProjectPersister()
				)
		);
	}
	
	private function getBlogId( $project_it )
	{
		return 'Devprom.News.'.$project_it->getId();
	}
	
	private function getTitle()
	{
		return str_replace('%1', getFactory()->getObject('SystemSettings')->getFirst()->getDisplayName(), text(299));
	}
	
 	private function convert ( $attributes )
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