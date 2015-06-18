<?php

include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';

define('BLACK', 	0);
define('DARKGRAY',	1);
define('LIGHTBLUE',	2);
define('CYAN',		3);
define('LIGHTGREEN',4);
define('PURPLE',	5);
define('RED', 		6);
define('YELLOW', 	7);
define('WHITE',		8);
define('BLUE', 		9);
define('DARKCYAN',  10);
define('DARKGREEN', 11);
define('DARKPURPLE',12);
define('BROWN',	    13);
define('DARKYELLOW',14);
define('GRAY',		15);
define('LIGHTGRAY', 16);

define('LEVEL', 'level');
define('HEADING1', 'heading 1');
define('HEADING2', 'heading 2');
define('HEADING3', 'heading 3');
define('NORMAL', 'Normal');

 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 class WikiConverterRtf
 {
 	var $rtf, $styles, $numlists, $wiki_it, $parser;
 	
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
 	
 	function setParser( $parser )
 	{
 		$this->parser = $parser;
 	}
 	
	function setRevision( $change_it )
 	{
 		$this->change_it = $change_it;
 	}
 	
 	function parse()
 	{
 		global $model_factory;
	
		$object_it = $this->getObjectIt();

 		if ( $this->getTitle() == '' ) $this->setTitle($object_it->getDisplayName());
		
		$this->prepare();
		
		$index = 1;
		
		while ( !$object_it->end() )
		{
			$this->transformWiki( $object_it, count($object_it->getParentsArray()) - 1, $object_it->get('SectionNumber') );

			$index++;
			
			$object_it->moveNext();
		}

		$this->buildStyles();
		
		$this->display();
 	}

	function transformWiki( $parent_it, $level, $index = '1' )
	{
		if ( $level > 0 )
		{
			$title = html_entity_decode($parent_it->get('Caption'), ENT_QUOTES | ENT_HTML401, APP_ENCODING);
			
			$this->rtf .= $this->styles[LEVEL.min($level, 10)]."{".$title."}\\par \\pard\\plain ".$this->styles[NORMAL];
		}
		
		if ( is_object($this->change_it) )
		{
		    $content = $this->change_it->getHtmlDecoded('Content');
		}
		else
		{
			$this->setObjectIt( $parent_it );
			
		    $content = $parent_it->getHtmlDecoded('Content');
		}

		$editor = WikiEditorBuilder::build($parent_it->get('ContentEditor'));

		$editor->setObjectIt( $parent_it );

 		$parser = $editor->getHtmlParser();
 		
 		$parser->setObjectIt( $parent_it );

 		$parser->setRequiredExternalAccess( false );
 		
 		$this->setParser($parser);
		
		$content = $parser->parse( $content );

        $this->transform( $content );	
	}

	function transform( &$html )
	{
		global $wiki_converter, $numlists;
		$wiki_converter = $this;

		$this->map = array (
			'<br[^>]*>\s*' => "\\par\n",
			'<hr[^>]*>\s*' => "\\par\n",
			'<p><h([\d])>' => "<h\\1>",
			'<\/h([\d])><\/p>' => "</h\\1>",
			'<p><\/p>' => "",
			'<p>[\r\n]?<\/p>' => "",
			'<p[^>]*>\s*' => "",
			'<\/?span[^>]*>\s*' => "",
			'<br\/><h([\d]{1})>' => "<h\\1>",
			'<del[^>]*>\s*' => "\\strike{",
			'<\/del[^>]*>' => "}\\strike0 ",
			'<sub[^>]*>' => "{\\sub ",
			'<\/sub[^>]*>' => "}",
			'<sup[^>]*>' => "{\\super ",
			'<\/sup[^>]*>' => "}",
			'<h6[^>]*>\s*' => "\\b{",
			'<\/h6[^>]*>' => "}\\b0 \\par\n",
			'<strong[^>]*>\s*' => "\\b{",
			'<\/strong[^>]*>' => "}\\b0 ",
			'<h1[^>]*>\s*' => $this->styles[HEADING1],
			'<h2[^>]*>\s*' => $this->styles[HEADING1],
			'<h3[^>]*>\s*' => $this->styles[HEADING3],
			'<h4[^>]*>\s*' => $this->styles[HEADING3],
			'<h5[^>]*>\s*' => $this->styles[HEADING3],
			'<\/h[\d]{1}>' => "\\sb240\\par\\plain\n".$this->styles[NORMAL],
			'<\/ol[^>]*>[^<]*<\/li>' => "}",
			'<\/ul[^>]*>[^<]*<\/li>' => "}",
			'<li[^>]*>\s*' => "\\jclisttab\\ilvl0{\\ql ",
			'<\/li[^>]*>' => "\\par}",
			'<div style="float:right;">\s*' => "\\qr",
			'<div[^>]*>\s*' => "",
			'<center[^>]*><img' => "<img",
			'\/><\/center[^>]*>' => "/>",
			'<center[^>]*>\s*' => "{",
			'<\/center[^>]*>' => "}\\qc\\par\n\\ql", 
			'<a[^>]*><\/a>' => "", 
			'<a[^>]+href="([^"]*)"[^>]*>\s*' => "{\\field {\\*\\fldinst {HYPERLINK \"\\1\"}}{\\fldrslt {\\ul\\cf2 ",
			'<\/a>' => "}}}\n",
			'<table[^>]*>' => "\\par\n\\pard\\plain ".$this->styles[NORMAL]." \\f".$this->get_font_id('times')."\\fs24\n",
			'<\/table[^>]*>' => "\\par\n".$this->styles[NORMAL],
			'<tbody[^>]*>\s*' => "",
			'<\/tbody[^>]*>' => "",
			'<thead[^>]*>\s*' => "",
			'<\/thead[^>]*>' => "",
			'<\/p>[\r|\n]+' => "\\sb240\\par\\plain\n".$this->styles[NORMAL],
			'<\/p[^>]*>' => "",
			'<\/div>[\r|\n]+' => "\\sb240\\par\\plain\n".$this->styles[NORMAL],
			'<\/div[^>]*>' => "",
			'<b[^>]*>\s*' => "\\b{",
			'<\/b[^>]*>' => "}\\b0 ",
			'<i[^>]*>\s*' => "\\i{",
			'<\/i[^>]*>' => "}\\i0 ", 
			'<\/ol[^>]*>' => "\\pard ".$this->styles[NORMAL],
			'<\/ul[^>]*>' => "\\pard ".$this->styles[NORMAL],
			'<u[^>]*>\s*' => "\\ul{",
			'<\/u[^>]*>' => "}\\ulnone ",
			'<\/?em[^>]*>' => "",
			'<\/?font[^>]*>' => ""
		);
		
		$html = str_replace( chr(9), '', $html );
		$html = str_replace("\\", "\x5c\x5c\x5c\x5c", $html);
		$html = str_replace("{", "\{", $html);
		$html = str_replace("}", "\}", $html);
		$html = str_replace("~", "\~", $html);
		$html = str_replace("&quot;", "\"", $html);
		$html = str_replace("&ldquo;", "\"", $html);
		$html = str_replace("&rdquo;", "\"", $html);
		$html = str_replace("&nbsp;", " ", $html);
		$html = str_replace("&#39;", "'", $html);
		
		// insert images
		$html = preg_replace_callback('/<img([^>]+)>/im', preg_image_rtf_callback, $html);
		
		$html = preg_replace("/[\&\#\?]{1,2}[a-z0-9]{2,8};/i", "", $html);

		// special case for tables
		$cellx = array();
		
		$trsfound = $this->untype_tag( "tr", $html );
		for ( $i = 1; $i <= $trsfound; $i++ )
		{
			$tablerow = array();
			preg_match('/<tr'.$i.'[^>]*>(.*)<\/tr'.$i.'>/si', $html, $tablerow);

			$columns = array();
			$widths = array();

			$tdsfound = $this->untype_tag( "t[d|h]", $tablerow[1] );
			for ( $j = 1; $j <= $tdsfound; $j++ )
			{
				$tablecol = array();
				preg_match('/<t[d|h]'.$j.'([^>]*)>(.*)<\/t[d|h]'.$j.'>/si', $tablerow[1], $tablecol);

				// looking for width
				if ( preg_match('/width\s*:\s*([^;]+)/si', $tablecol[1], $width_string) )
				{
					array_push($widths, $width_string[1]);
				}
				else
				{
					array_push($widths, 0);
				}
				
				$tablecol[2] = preg_replace( '/<\/p>/i', '\\par', $tablecol[2] );
				$tablecol[2] = preg_replace( '/<\/div>/i', '\\par', $tablecol[2] );
				
				array_push($columns, trim($tablecol[2]));
				
				if( preg_match('/colspan="([^"]+)"/si', $tablecol[1], $colspan) !== false )
				{
					for( $s = 1; $s < $colspan[1]; $s++ ) {
						array_push($columns, "");
					}
				}
			}

			$max_width = 10800;
			$left_width = $max_width;
			$columns_num = count($columns);

			foreach ( $widths as $key => $width )
			{
				if ( is_string($width) )
				{ 
					$parts = preg_split('/%/', $width);
					if ( is_numeric($parts[0]) )
					{
						$width = ((int) $parts[0]) * $max_width / 100;
						$left_width -= $width;
						$columns_num -= 1;
					} 
					else
					{
						$width = 0;
					}
				}
				
				if ( $width < 1 ) $width = ($left_width / $columns_num);
				if ( $cellx[$key] < 1 ) $cellx[$key] = ($key < 1 ? 0 : $cellx[$key - 1]) + $width;
			}
			
			$rtf_table_row = "\\trowd";
			for ( $j = 0; $j < count($columns); $j++ )
			{
				$rtf_table_row .= "\\irow1\\irowband1\\ \\clbrdrt\\brdrs\\brdrw1\\brdrcf1\\clbrdrl\\brdrs\\brdrw1\\brdrcf1\\clbrdrb\\brdrs\\brdrw1\\brdrcf1\\clbrdrr\\brdrs\\brdrw1\\brdrcf1\\cellx".$cellx[$j];
			}
			$rtf_table_row .= "\n";
			for ( $j = 0; $j < count($columns); $j++ )
			{
				$rtf_table_row .= "{\\ql\\li0\\ri0\\nowidctlpar\\intbl\\wrapdefault\\faauto\\rin0\\lin0 ".$columns[$j]."}\n";
				if ( $j < count($columns) - 1 )
				{
					$rtf_table_row .= "\\cell\\pard\\plain ";
				}
			}
			$rtf_table_row .= "\\cell\\row\\pard ";
			$html = preg_replace('/<tr'.$i.'[^>]*>(.*)<\/tr'.$i.'>/si', $rtf_table_row, $html);
		}

		$codeblocks = array();
		
		// extract blocks of code, because it should'n be parsed
		$tagsfound = $this->untype_tag( "pre", $html );
		for ( $i = 1; $i <= $tagsfound; $i++ )
		{
			$code = array();

			preg_match('/<pre'.$i.'[^>]*>(.+)<\/pre'.$i.'>/si', $html, $code);
			$code[1] = str_replace(chr(10), "\\par\n", html_entity_decode($code[1], ENT_QUOTES | ENT_HTML401, APP_ENCODING));
			
			array_push($codeblocks, $code[1]);
			
			$html = preg_replace('/<pre'.$i.'[^>]*>(.+)<\/pre'.$i.'>/si', 
				"<xpre".$i."></xpre".$i.">", $html);
		}

		// insert numbered lists
		$this->processLists( $html );
	
		// transform html tags to rtf's ones
		foreach ( array_keys($this->map) as $tag )
		{
			$html = preg_replace('/'.$tag.'/i', $this->map[$tag], $html);
		}

		// return blocks of code
		for( $i = 1; $i <= count($codeblocks); $i++ )
		{
			$code = "\\f".$this->get_font_id('courier new')."\\fs20\n".
					$codeblocks[$i - 1]."\\par\n\\pard\\plain ".$this->styles[NORMAL];

			$html = preg_replace('/<xpre'.$i.'><\/xpre'.$i.'>/i', $code, $html);
		}

		$this->rtf .= $html;
		if ( $html != '' )
		{
			$this->rtf .= " \\par";
		}
	}
	
	function processLists( &$html )
	{
		global $numlists;
		
		$this->untype_tag( "ol", $html );
		$this->untype_tag( "ul", $html );
		
		$tags = preg_split('/<(\/?ol[^>]+|\/?ul[^>]+)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
		
		$level = 0;
		foreach( $tags as $tindex => $tvalue )
		{
			if( $tindex % 2 == 0 )
			{
				switch ( $tag_type )
				{
					case 'OL':
					 	$numlists++;
					 	
						$html = preg_replace('/<'.$current_tag.'([^>]*)>/si', 
					 		($level > 1 ? "\\par" : "")."\\sectd\\pard\\plain\\fi-360\\li".(720 * $level).
					 			"\\tx720\\ls".($numlists+1)."\\levelstartat1\\listrestarthdn1 ", $html );
					 	break;
					
				 	case 'UL':
						$html = preg_replace('/<'.$current_tag.'([^>]*)>/si', 
					 		($level > 1 ? "\\par" : "")."\\sectd\\pard\\plain\\fi-360\\li".(720 * $level).
					 			"\\tx720\\ls2", $html );
					 	break;
				}
			}
			else
			{
				if( $tvalue[0] == '/')
				{
					// close
					$current_tag = '';
					$tag_type = '';
					$level--;
				}
				else
				{
					$a2 = explode(' ', $tvalue);
					$tag_name = trim(strtoupper(array_shift($a2)), '/');
					
					$current_tag = $tag_name;
					$tag_type = substr($tag_name, 0, 2);
					$level++;
				}
			}
		}
		
	}
	
	function untype_tag( $tag, &$html )
	{
		global $blocks;
		
		$blocks = 0;
		
		$html = preg_replace_callback('/<('.$tag.')([^>]*)>\r?\n?/im', 
			preg_untype_rtf_callback, $html);
		
		$blocks = 0;
		
		$html = preg_replace_callback('/<(\/'.$tag.')>\r?\n?/im', 
			preg_untype_rtf_callback, $html);

		return $blocks;
	}
	
	function prepare()
	{ 	
		global $numlists;
		
		$numlists = 1;
		
        // styles table
		$this->styles = array (
			HEADING1 => 
				"\\ql\\sb340\\li0\\ri0\\sa120\\sb240\\b\\fs32\\lang1049\\langfe2052\\kerning32\\loch\\f1\\hich\\af1\\dbch\\af13\\cgrid\\langnp1049\\snext0 ",
			HEADING2 => 
				"\\ql\\sb340\\li0\\ri0\\sa60\\sb240\\b\\fs28\\lang1049\\langfe2052\\loch\\f1\\hich\\af1\\dbch\\af13\\cgrid\\langnp1049\\snext0 ",
			HEADING3 => 
				"\\ql\\b\\sb340\\sa60\\fs26\\lang1049\\langfe2052\\loch\\f1\\hich\\af1\\dbch\\af13\\cgrid\\langnp1049\\snext0 ",
			NORMAL => 
				"\\ql\\sb0\\fs24\\lang1049\\snext0 ",
			'Normal Table' => 
				"\\*\\ts11\\tsrowd\\trftsWidthB3\\trautofit1\\trpaddl108\\trpaddr108\\trpaddfl3\\trpaddft3\\trpaddfb3\\trpaddfr3\\tscellwidthfts0\\tsvertalt\\tsbrdrt\\tsbrdrl\\tsbrdrb\\tsbrdrr\\tsbrdrdgl\\tsbrdrdgr\\tsbrdrh\\tsbrdrv " .
					"\\fs20\\lang1024\\langfe1024\\loch\\f0\\hich\\af0\\dbch\\af13\\cgrid\\langnp1024\\langfenp1024 ".
					"\\ql \\snext11 \\ssemihidden",
			'Table Grid' => 
				"\\*\\ts12\\tsrowd\\trautofit1 \\clbrdrb\\brdrs\\brdrw10\\wrapdefault " .
					"\\trbrdrt\\brdrs\\brdrw10 " .
					"\\trbrdrl\\brdrs\\brdrw10 " .
					"\\trbrdrb\\brdrs\\brdrw10 " .
					"\\trbrdrr\\brdrs\\brdrw10 " .
					"\\trbrdrh\\brdrs\\brdrw10 " .
					"\\trbrdrv\\brdrs\\brdrw10 " .
					"\\trftsWidthB3\\trpaddl108\\trpaddr108\\trpaddfl3\\trpaddft3\\trpaddfb3\\trpaddfr3\\tscellwidthfts0\\tsvertalt\\tsbrdrt\\tsbrdrl\\tsbrdrb\\tsbrdrr\\tsbrdrdgl\\tsbrdrdgr\\tsbrdrh\\tsbrdrv " .
				"\\ql\\sb320 \\sbasedon11 \\snext12"
			);
			
		$numbering = defined('REQ_RTF_NUMBERING') && REQ_RTF_NUMBERING ? "\\ls1" : ""; 
			
		for ( $i = 1; $i <= 10; $i++ )
		{
			$this->styles[LEVEL.$i] = 
				"\\ilvl".($i-1)."\\s".$i."\\b\\sa120\\sb240\\fs32\\ql".$numbering."\\nowidctlpar\\wrapdefault\\faauto\\outlinelevel".($i-1);
		}
	}
	
	function buildStyles()
	{
		global $numlists;
		
		$this->style = "{\\rtf1\\ansicpg1251\\deff0\\deflang1049\\lang1049\\langfe2052\n";
		
		// color table
		$this->style .= "{\\colortbl;\n".
                      "\\red0\\green0\\blue0;\\red0\\green0\\blue255;\\red0\\green255\\blue255;\n".
                      "\\red0\\green255\\blue0;\\red255\\green0\\blue255;\\red255\green0\\blue0;\n".
                      "\\red255\\green255\\blue0;\\red255\\green255\\blue255;\\red0\green0\\blue128;\n".
                      "\\red0\\green128\\blue128;\\red0\\green128\\blue0;\\red128\\green0\\blue128;\n".
                      "\\red128\\green0\\blue0;\\red128\\green128\\blue0;\\red128\\green128\\blue128;\n".
                      "\\red192\\green192\\blue192;\n".
                      "}\n";
                      
        // font table
		$this->style .= "{\\fonttbl\n".
					  "{\\f0\\froman\\fcharset204\\fprq2 Times New Roman;}\n".
					  "{\\f1\\fswiss\\fcharset204\\fprq2 Arial;}\n".
					  "{\\f2\\fswiss\\fcharset204\\fprq2 Arial Black;}\n".
					  "{\\f3\\fswiss\\fcharset204\\fprq2 Verdana;}\n".
					  "{\\f4\\fswiss\\fcharset204\\fprq2 Tahoma;}\n".
					  "{\\f5\\fmodern\\fcharset204\\fprq2 Courier New;}\n".
					  "{\\f6\\froman\\fcharset2\\fprq2 Symbol;}\n".
					  "}";
	
		$this->style .= "{\\stylesheet";
		
		foreach ( $this->styles as $name => $style )
		{
			$this->style .= "{".$style." ".$name.";}";
		}
		
		$this->style .= "}";
		
		// lists table
        $this->style .= 
			"{\\*\\listtable\n".
				"{\\list\\listhybrid" .
					"{\\listlevel\\levelnfc23\\leveljc0\\leveljcn0\\levelfollow0\\levelstartat1\\levelspace360\\levelindent0" .
						"{\\leveltext\\'01\\u-3913 ?;}{\\levelnumbers;}" .
					"\\f6\\fbias0 \\fi-360\\li720\\jclisttab\\tx720\\lin720 }" .
					"{\\listlevel\\levelnfc23\\leveljc0\\leveljcn0\\levelfollow0\\levelstartat1\\levelspace360\\levelindent0" .
						"{\\leveltext\\'01\\u-3913 ?;}{\\levelnumbers;}" .
					"\\f6\\fbias0 \\fi-360\\li1440\\jclisttab\\tx1440\\lin1440 }" .
					"{\\listname ;}" .
				"\\listid2}";

		for ( $i = 0; $i < $numlists; $i++ )
		{
			$this->style .=
				"{\\list\\listhybrid\\listrestarthdn" .
					"{\\listlevel\\levelnfc0\\leveljc0\\leveljcn0\\levelfollow0\\levelstartat1\\levelspace360\\levelindent0" .
						"{\\leveltext\\'02\\'00.;}" .
						"{\\levelnumbers\\'01;}" .
					"\\fi-360\\li720\\jclisttab\\tx720\\lin720\\sb240 }" .
					"{\\listlevel\\levelnfc23\\leveljc0\\leveljcn0\\levelfollow0\\levelstartat1\\levelspace360\\levelindent0" .
						"{\\leveltext\\'01\\u-3913 ?;}" .
						"{\\levelnumbers;}" .
					"\\f6\\fbias0 \\fi-360\\li1440\\jclisttab\\tx1440\\lin1440 }" .
					"{\\listname ;}" .
				"\\listid".($i+3)."}";
		}
		
		$this->style .= 
			"{\\list".
				"{\\listlevel\\levelnfc0\\levelnfcn0\\leveljc0\\leveljcn0\\levelfollow0\\levelstartat1\\levelspace0\\levelindent0".
					"{\\leveltext\\'02\\'00.;}{\\levelnumbers\\'01;}\\s1\\b\\fi-280\\li280\\lin280}".
				"{\\listlevel\\levelnfc0\\levelnfcn0\\leveljc0\\leveljcn0\\levelfollow0\\levelstartat1\\levelspace0\\levelindent0".
					"{\\leveltext\\'04\\'00.\\'01 ;}{\\levelnumbers\\'01\\'03;}\\s2\\b\\fi-576\\li576\\lin576}".
				"{\\listlevel\\levelnfc0\\levelnfcn0\\leveljc0\\leveljcn0\\levelfollow0\\levelstartat1\\levelspace0\\levelindent0".
					"{\\leveltext\\'06\\'00.\\'01.\\'02 ;}{\\levelnumbers\\'01\\'03\\'05;}\\s3\\fi-720\\li720\\lin720}".
				"{\\listlevel\\levelnfc0\\levelnfcn0\\leveljc0\\leveljcn0\\levelfollow0\\levelstartat1\\levelspace0\\levelindent0".
					"{\\leveltext\\'08\\'00.\\'01.\\'02.\\'03 ;}{\\levelnumbers\\'01\\'03\\'05\\'07;}\\s4\\fi-864\\li864\\lin864 }".
				"{\\listlevel\\levelnfc0\\levelnfcn0\\leveljc0\\leveljcn0\\levelfollow0\\levelstartat1\\levelspace0\\levelindent0".
					"{\\leveltext\\'0a\\'00.\\'01.\\'02.\\'03.\\'04 ;}{\\levelnumbers\\'01\\'03\\'05\\'07\\'09;}\\s5\\fi-1008\\li1008\\lin1008 }".
				"{\\listlevel\\levelnfc0\\levelnfcn0\\leveljc0\\leveljcn0\\levelfollow0\\levelstartat1\\levelspace0\\levelindent0".
					"{\\leveltext\\'0c\\'00.\\'01.\\'02.\\'03.\\'04.\\'05 ;}{\\levelnumbers\\'01\\'03\\'05\\'07\\'09\\'0b;}\\s6\\fi-1152\\li1152\\lin1152 }".
				"{\\listlevel\\levelnfc0\\levelnfcn0\\leveljc0\\leveljcn0\\levelfollow0\\levelstartat1\\levelspace0\\levelindent0".
					"{\\leveltext\\'0e\\'00.\\'01.\\'02.\\'03.\\'04.\\'05.\\'06 ;}{\\levelnumbers\\'01\\'03\\'05\\'07\\'09\\'0b\\'0d;}\\s7\\fi-1296\\li1296\\lin1296 }".
				"{\\listlevel\\levelnfc0\\levelnfcn0\\leveljc0\\leveljcn0\\levelfollow0\\levelstartat1\\levelspace0\\levelindent0".
					"{\\leveltext\\'10\\'00.\\'01.\\'02.\\'03.\\'04.\\'05.\\'06.\\'07 ;}{\\levelnumbers\\'01\\'03\\'05\\'07\\'09\\'0b\\'0d\\'0f;}\\s8\\fi-1440\\li1440\\lin1440 }".
				"{\\listlevel\\levelnfc0\\levelnfcn0\\leveljc0\\leveljcn0\\levelfollow0\\levelstartat1\\levelspace0\\levelindent0".
					"{\\leveltext\\'12\\'00.\\'01.\\'02.\\'03.\\'04.\\'05.\\'06.\\'07.\\'08 ;}{\\levelnumbers\\'01\\'03\\'05\\'07\\'09\\'0b\\'0d\\'0f\\'11;}\\s9\\fi-1584\\li1584\\lin1584 }".
			"{\\listname ;}\\listid1}";
		
		$this->style .= "}\n".
			"{\\*\\listoverridetable" .
				"{\\listoverride\\listid2\\listoverridecount0\\ls2}";
		
		$this->style .=
			"{\\listoverride\\listid1\\listoverridecount0\\ls1}";
		
		for ( $i = 0; $i < $numlists; $i++ )
		{
			$this->style .=
				"{\\listoverride\\listid".($i+3)."\\listoverridecount0\\ls".($i+3)."}";
		}
		
		$this->style .= "}\n";

		// margins
        $this->style .= "\\paperw12240\\paperh15840\n";
        $this->style .= "\\margl".'720'."\\margr".'720'."\\margt".'720'."\\margb".'720'."\n";
				
        $this->style .= "\n{\n\n";
        
        $this->rtf = $this->style.$this->rtf;
    }
	
 	function display()
 	{
		$this->rtf .= "\n}\n}\n";

		$file_name = preg_replace('/[\.\,\+\)\(\)\:\;]/i', '_', 
			html_entity_decode($this->getTitle(), ENT_QUOTES | ENT_HTML401, APP_ENCODING)).'.rtf';

 		if ( EnvironmentSettings::getBrowserPostUnicode() )
		{ 
			$file_name = IteratorBase::wintoutf8($file_name);
		}
		
		header("Content-type: application/msword");
		header("Content-Lenght: ". sizeof($this->MyRTF));
   	    header("Content-Disposition: inline; filename=\"".$file_name.'"');
   	    
   	    echo $this->rtf;
 	}

	function get_font_id( $font_name = NULL )
	{
		switch ( strtolower($font_name) )
      	{
			case 'times':        return(0);
   	        case 'arial':        return(1);
			case 'arial black':  return(2);
			case 'verdana':      return(3);
			case 'tahoma':       return(4);
 			case 'courier new':  return(5);
 			default:             return(0);
		}
	}
 }

 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 function preg_image_rtf_callback( $match ) 
 {
 	global $wiki_converter;
 	
 	$result = "";
 	
 	$width_match = array();
 	
 	if ( preg_match('/width="([^"]+)"/mi', $match[1], $width_match) > 0 )
 	{
 		$parts = preg_split('/\%/', $width_match[1]);
 		
 		if ( count($parts) > 1 )
 		{
 			$ratio = $parts[0] / 2;
 		}
 		else
 		{
 			$width = $parts[0] * 17;
 		}
 	}
 	
 	$source_match = array();

 	if ( preg_match('/src="([^"]+)"/mi', $match[1], $source_match) !== false )
 	{
 		$content = '';
 		
		$file_it = $wiki_converter->parser->getFileByHref( $source_match[1] );
		
 		if ( !is_object($file_it) )
 		{
 			$path = $source_match[1]; 

 			if ( filter_var($source_match[1], FILTER_VALIDATE_URL) !== false )
 			{
 			    $content = file_get_contents($source_match[1]);
 			}
 			
 			if ( $content == '' ) {
 				return " \\qc{}\\ql";
 			}
 		}
 		else
 		{
	 		$path = SERVER_FILES_PATH.$file_it->object->getClassName().'/'.
	 			basename($file_it->getFilePath( 'Content' ));
 		}
 		
 		if ( $content == '' )
 		{
	 		if ( !file_exists($path) )
	 		{
	 			return $result;
	 		}
			
	 		$content = file_get_contents($path);
 		}
 		
		$result = "{";
		
		if ( $ratio > 0 )
		{
			$result .= "\\pict\\jpegblip\\picscalex". $ratio ."\\picscaley". $ratio ."\\bliptag132000428 ";
		}
		elseif ( $width > 0 )
		{
			list ($orgwidth, $orgheight) = getimagesize($path);
			$scale = $orgwidth / $orgheight; 

			$result .= "\\pict\\jpegblip\\picwgoal". $width ."\\pichgoal".round($width/$scale, 0)."\\bliptag132000428 ";
		}
		else
		{
			list ($orgwidth, $orgheight) = getimagesize($path);
			$scale = $orgwidth / $orgheight;
			$width = min($orgwidth * 15, 10820);

			$result .= "\\pict\\jpegblip\\picwgoal". $width ."\\pichgoal".round($width/$scale, 0)."\\bliptag132000428 ";
		}

		$result .= "\\bin ".trim(bin2hex($content));
		$result .= "}";
 	}
 	else
 	{
 		return $result;
 	}


 	return $result;
 }

 function preg_list_rtf_callback( $match ) 
 {
 	global $numlists;
 	
 	$numlists++;
 	
 	return "\\sectd \\pard\\plain \\fi-360\\li720\\tx720\\ls".($numlists+1)."\\levelstartat1\\listrestarthdn1";
 }

 function preg_untype_rtf_callback( $match ) 
 {
 	global $blocks;
 	
 	$blocks++;
 	return "<".$match[1].$blocks.$match[2].">";
 }
 
?>