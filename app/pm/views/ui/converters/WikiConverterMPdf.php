<?php
use \Mpdf\Mpdf;
include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';

class WikiConverterMPdf
{
 	var $wiki_it, $parser, $pdf;
 	private $title = '';
 	private $level = 0;
    private $options = array();

    function setOptions( $options ) {
        $this->options = $options;
    }

    function getOptions() {
        return $this->options;
    }

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
        $this->level = count($wiki_it->getParentsArray());
        $this->wiki_it = $wiki_it;
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
 	    ini_set('pcre.backtrack_limit', 1073741824);

 	    $this->uid = new ObjectUID();
		$this->pdf = new mPDF();
        $this->pdf->SetAnchor2Bookmark(1);

        if ( in_array('paging', $this->options) ) {
            $this->pdf->setFooter('{PAGENO}');
        }

 		$object_it = $this->getObjectIt();
 		if ( $this->getTitle() == '' ) $this->setTitle($object_it->getDisplayName());

 		while( !$object_it->end() )
		{
			$this->transformWiki( $object_it->copy(), count($object_it->getParentsArray()) - $this->level);
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
        $parser->setReferenceTitleResolver(
            in_array('uid', $this->options)
                ? function($info) {
                $result = $info['caption'];
                if ( $info['uid'] != '' ) {
                    $result = "[" . $info['uid'] . "] " . $result;
                }
                return $result;
            }
                : function($info) {
                return $info['caption'];
            }
        );

		$content = $parser->parse( $content );
        $title = $parent_it->getHtmlDecoded('Caption');
        $heading_level = max(min($level, 4), 1) + 1;

        $this->transform(
            mb_convert_encoding(
                    '<a name="'.htmlentities($title).'" level="'.$level.'"/><h'.$heading_level.' '.($this->headers_passed < 1 || true ? 'style="page-break-before:avoid;"' : '').'>'.
                       $this->getItemTitle($parent_it).
                    '</h'.$heading_level.'>'.
                    ''.$content.'',
                    'UTF-8',
                    APP_ENCODING)
            );
        $this->headers_passed++;
	}

	function getItemTitle( $parent_it )
    {
        $title = '';
        if( in_array('numbering', $this->options) && $parent_it->get('SectionNumber') != '' ) {
            $title .= $parent_it->get('SectionNumber').'.&nbsp; ';
        }
        if ( in_array('uid', $this->options) && $parent_it->get('IsNoIdentity') == 'N' ) {
            $info = $this->uid->getUIDInfo($parent_it);
            $title .= $info['uid'] . '&nbsp; ';
        }
        $title .= $parent_it->getHtmlDecoded('Caption');
        return $title;

    }

	function transform( &$html )
	{
		global $wiki_converter;
		$wiki_converter = $this;

		$this->pdf->WriteHTML($html, 2);
	}

 	function display()
 	{
		$file_name = preg_replace('/[\.\,\+\)\(\)\:\;]/i', '_', html_entity_decode($this->getTitle(), ENT_QUOTES | ENT_HTML401, APP_ENCODING)).'.pdf';
		$file_name = EnvironmentSettings::getBrowserIE() ? rawurlencode($file_name) : $file_name;
		$this->pdf->Output($file_name, 'D');
 	}
}
 