<?php

include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';

include( SERVER_ROOT_PATH.'ext/mpdf50/mpdf.php' );
 
use \mpdf\mPDF;
 
class WikiConverterMPdf
{
 	var $wiki_it, $parser, $pdf;
 	
 	private $title = '';
 	
 	function setTitle( $title )
 	{
 		$this->title = $title;
 	}
 	
 	function getTitle()
 	{
 		return $this->title;
 	}
 	
 	function setObjectIt( $wiki_it )
 	{
 		if ( $wiki_it->count() > 1 )
 		{
 			$this->wiki_it = $wiki_it;
 		}
 		else
 		{
	 		$this->wiki_it = $wiki_it->object->getRegistry()->Query( 
	 				array_merge( 
	 						array(
					 				new WikiRootTransitiveFilter($wiki_it->getId()),
					 				new SortDocumentClause()
	 						) 
	 				)
	 		);
 		}
 	}

 	function getObjectIt()
 	{
 		return $this->wiki_it;
 	}
 	
 	function setRevision( $change_it )
 	{
 		$this->change_it = $change_it;
 	}
 	
 	function parse()
 	{
 		global $model_factory;
	
		$this->pdf = new mPDF('ru', 'A4');

		$this->pdf->WriteHTML(file_get_contents(SERVER_ROOT_PATH.'styles/newlook/main.css'), 1);
		
		$this->pdf->WriteHTML(file_get_contents(SERVER_ROOT_PATH.'styles/newlook/extended.css'), 1);
		
		$this->pdf->WriteHTML(file_get_contents(SERVER_ROOT_PATH.'styles/wysiwyg/msword.css'), 1);
		
		$this->pdf->WriteHTML(' body {background:white;font-size:14px;line-height:160%;} td {font-size:14px;line-height:160%;} ', 1);

 		$object_it = $this->getObjectIt();
		
 		if ( $this->getTitle() == '' ) $this->setTitle($object_it->getDisplayName());
 		
 		while( !$object_it->end() )
		{
			$this->transformWiki( $object_it, count($object_it->getParentsArray()) - 1 );

			$object_it->moveNext();
		}
		
		$this->display();
 	}

	function transformWiki( $parent_it, $level = 0 )
	{
		if ( is_object($this->change_it) )
		{
			$content = $this->change_it->getHtmlDecoded('Content');
		}
		else
		{
			$content = $parent_it->getHtmlDecoded('Content');
		}
		
		$editor = WikiEditorBuilder::build($parent_it->get('ContentEditor'));

		$editor->setObjectIt( $parent_it );

 		$parser = $editor->getHtmlParser();
 		
 		$parser->setObjectIt( $parent_it );
 		$parser->setRequiredExternalAccess();
		$parser->setHrefResolver(function($wiki_it) {
 			return '#'.$wiki_it->getHtmlDecoded('Caption');
 		});
 		$parser->setReferenceTitleResolver(function($info) {
 			return $info['caption'];
 		});
 		
		$content = $parser->parse( $content );
    		
		if ( $level > 0 || $content != '' )
		{
    		$title = $parent_it->getHtmlDecoded('Caption');
    		
    		$heading_level = max(min($level, 4), 1);  
    		
    		$this->transform( 
    			mb_convert_encoding(
    			        '<h'.$heading_level.' '.($this->headers_passed < 1 ? 'style="page-break-before:avoid;"' : '').'><a name="'.$title.'" level="'.$level.'"></a>'.$title.'</h'.$heading_level.'>'.
    			        ''.$content.'', 
    			        'UTF-8', 
    			        'windows-1251') 
    			);
    		
    		$this->headers_passed++;
		}
	}

	function transform( &$html )
	{
		global $wiki_converter;
		$wiki_converter = $this;

		$this->pdf->WriteHTML($html, 2);
	}

 	function display()
 	{
		$file_name = preg_replace('/[\.\,\+\)\(\)\:\;]/i', '_', html_entity_decode($this->getTitle(), ENT_QUOTES | ENT_HTML401, 'cp1251')).'.pdf';

		if ( EnvironmentSettings::getBrowserPostUnicode() )
		{ 
			$file_name = IteratorBase::wintoutf8($file_name);
		}
		
		$this->pdf->Output($file_name, 'D');
 	}
}
 