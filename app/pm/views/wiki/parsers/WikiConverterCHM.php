<?php

include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';

require_once(SERVER_ROOT_PATH.'ext/zip/createzipfile.php');

class WikiConverterCHM
{
 	var $path, $content_tree, $files, $template_it, $wiki_it;
 	
 	function WikiConverterCHM( $template )
 	{
 		global $model_factory;

 		$templ_cls = $model_factory->getObject('TemplateHTML');
 		$this->template_it = $templ_cls->getExact($template);
 	}
 	
 	function setObjectIt( $wiki_it )
 	{
 		$this->wiki_it = $wiki_it;
 	}

 	function getObjectIt()
 	{
 		return $this->wiki_it;
 	}

 	function parse()
 	{
 		$this->content_tree = array();
 		$this->files = array();
 		
 		$object_it = $this->getObjectIt();
 		$object_it = $object_it->_clone();
 		
		$file_name = $this->transform( $object_it );

		$this->content_tree[$file_name] = 
			array( 'name' => $object_it->getDisplayName(),
				   'items' => array() ) ;

		$this->transformWiki( $object_it, $this->content_tree );
		$this->display();
 	}

	function getPath()
	{
		if ( $this->path == '' )
		{
			$this->path = SERVER_BACKUP_PATH.md5(microtime()).'/';
			mkdir($this->path);
		}
		
		return $this->path;
	}
	
	function transformWiki( $parent_it, &$content_tree )
	{
		$parent_id = $parent_it->getId();
		$wiki_it = $parent_it->getChildrenIt();
		
		while ( $wiki_it->get('ParentPage') == $parent_id )
		{
			if ( $wiki_it->IsArchived() )
			{
				$wiki_it->moveNext();
				continue;
			}
			
			$id = $wiki_it->getId();
			$file_name = $this->transform( $wiki_it );	

			$content_tree[$file_name] = 
				array( 'name' => $wiki_it->getDisplayName(),
					   'items' => array() ) ;

			$this->transformWiki( $wiki_it, $content_tree[$file_name]['items'] );

			$wiki_it->moveToId($id);
			$wiki_it->moveNext();
		}
	}

	function transform( $wiki_it )
	{
		$file_name = self::translit($wiki_it->getDisplayName()).'_'.$wiki_it->getId().'.html';
		$file = fopen( $this->getPath().$file_name, 'w+' );

		$html = "<HTML><TITLE></TITLE>";
		
		if ( $this->template_it->count() > 0 )
		{
			$html .= "<STYLE>".$this->template_it->getHtmlDecoded('CSSBlock')."</STYLE>";
		}

		$html .= "<BODY>";
		
		if ( $this->template_it->count() > 0 )
		{
			$html .= $this->template_it->getHtmlDecoded('Header');
		}
		
		$editor = WikiEditorBuilder::build($wiki_it->get('ContentEditor'));

		$editor->setObjectIt($wiki_it);
		
 		$parser = $editor->getHtmlSelfSufficientParser();

 		$parser->setObjectIt($wiki_it);
 		
 		$parser->setRequiredExternalAccess();
 		
		$parser->setHrefResolver(function($wiki_it) {
 			return WikiConverterCHM::translit($wiki_it->getDisplayName()).'_'.$wiki_it->getId().'.html';
 		});
		
 		$parser->setObjectIt( $wiki_it );
		
		$html .= html_entity_decode($parser->parse( $wiki_it->getHtmlDecoded('Content')), ENT_QUOTES | ENT_HTML401, 'cp1251'); 
				
		if ( $this->template_it->count() > 0 )
		{
			$html .= $this->template_it->getHtmlDecoded('Footer');
		}

		fwrite( $file, $html );
		fclose( $file );
		
		array_push( $this->files, $file_name );
		
		return $file_name;
	}
	
	function buildContentsFile( $data, &$html )
	{
		foreach ( array_keys($data) as $key )
		{
			$html .= '<LI> <OBJECT type="text/sitemap"><param name="Name" value="'.$data[$key]['name'].'"><param name="Local" value="'.$key.'"></OBJECT>'."\r\n";
			if ( count($data[$key]['items']) > 0 )
			{
				$html .= '<UL>'."\r\n";
				$this->buildContentsFile( $data[$key]['items'], $html);
				$html .= '</UL>'."\r\n";
			}
		}
	}
	
 	function display()
 	{
		// create contents file
		$html = '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML//EN"><HTML><HEAD><META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251">';
		$html .= "\r\n<!-- Sitemap 1.0 -->\r\n";
		$html .= '</HEAD><BODY><OBJECT type="text/site properties"><param name="ImageType" value="Folder"></OBJECT>';
		$html .= "<UL>";
		$this->buildContentsFile( $this->content_tree, $html );
		$html .= "</UL></BODY></HTML>";
		
		$file = fopen( $this->getPath().'help.hhc', 'w+' );
		fwrite($file, $html);
		fclose($file);
		
		// create project file
		$file = fopen( $this->getPath().'help.hhp', 'w+' );
		fwrite($file, "[OPTIONS]\r\n");
		fwrite($file, "Compatibility=1.1 or later\r\n");
		fwrite($file, "Compiled file=help.chm\r\n");
		fwrite($file, "Contents file=help.hhc\r\n");
		fwrite($file, "Default Window=user-friendly\r\n");
		fwrite($file, "Display compile progress=Yes\r\n");
		fwrite($file, "Full-text search=Yes\r\n");
		fwrite($file, "Language=0x419\r\n");
		fwrite($file, "\r\n");
		fwrite($file, "[WINDOWS]\r\n");
		fwrite($file, "user-friendly=\"".$this->wiki_it->getDisplayName()."\"," .
			"\"help.hhc\",,\"".$this->files[0]."\",,,,,,0x42520,,0x206e,[215,55,1143,698],0x10cb0000,,,,,,0\r\n");
		fwrite($file, "\r\n");
		fwrite($file, "[FILES]\r\n");
		foreach ( $this->files as $projectfile )
		{
			fwrite($file, $projectfile."\r\n");
		}
		fwrite($file, "\r\n");
		fwrite($file, "[INFOTYPES]\r\n");
		fwrite($file, "\r\n");
		fclose($file);

		$file_name = $this->wiki_it->getDisplayName().'.zip';
		$mydir = dir($this->getPath());
		
 		$zip = new createFileZip( $this->getPath().$file_name );
 		
   		while(($file = $mydir->read()) !== false) 
   		{
   			if($file == '.' || $file == '..' || $file == $file_name ) continue;

   			$file_path = $this->getPath().$file;
			$f = fopen( $file_path, "r" );
		 	$zip->addFile(fread($f, filesize($file_path)), $file);
		 	fclose($f);
   		}

    	$mydir->close();
	 	$zip->saveZippedfile();

   	    $downloader = new Downloader;
   	    $downloader->echoFile( $this->getPath().$file_name, $file_name, 'application/zip');

   	    FileSystem::rmdirr($this->getPath());
 	}
	
	static function translit($string)
	{
       static $ru = array(
               'À', 'à', 'Á', 'á', 'Â', 'â', 'Ã', 'ã', 'Ä', 'ä', 'Å', 'å', '¨', '¸', 'Æ', 'æ', 'Ç', 'ç',
               'È', 'è', 'É', 'é', 'Ê', 'ê', 'Ë', 'ë', 'Ì', 'ì', 'Í', 'í', 'Î', 'î', 'Ï', 'ï', 'Ð', 'ð',
               'Ñ', 'ñ', 'Ò', 'ò', 'Ó', 'ó', 'Ô', 'ô', 'Õ', 'õ', 'Ö', 'ö', '×', '÷', 'Ø', 'ø', 'Ù', 'ù',
               'Ú', 'ú', 'Û', 'û', 'Ü', 'ü', 'Ý', 'ý', 'Þ', 'þ', 'ß', 'ÿ'
       );

       static $en = array(
               'A', 'a', 'B', 'b', 'V', 'v', 'G', 'g', 'D', 'd', 'E', 'e', 'E', 'e', 'Zh', 'zh', 'Z', 'z',
               'I', 'i', 'J', 'j', 'K', 'k', 'L', 'l', 'M', 'm', 'N', 'n', 'O', 'o', 'P', 'p', 'R', 'r',
               'S', 's', 'T', 't', 'U', 'u', 'F', 'f', 'H', 'h', 'C', 'c', 'Ch', 'ch', 'Sh', 'sh', 'Sch', 'sch',
               '\'', '\'', 'Y', 'y',  '\'', '\'', 'E', 'e', 'Ju', 'ju', 'Ja', 'ja'
       );

       $string = str_replace($ru, $en, $string);
       $string = preg_replace('/[^\w]+/', '_', $string);
       
       return $string;
	}
}
