<?php

 include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';

 include( SERVER_ROOT_PATH.'ext/pdf/fpdf.php' );
 
 define("FONT_FAMILY", "Times");
 define("FONT_HEADER0", 20);
 define("FONT_HEADER1", 18);
 define("FONT_HEADER2", 17);
 define("FONT_HEADER3", 16);
 define("FONT_HEADER4", 15);
 define("FONT_HEADER5", 14);
 define("FONT_HEADER6", 13);
 define("FONT_NORMAL", 12);
 define("LINE_HEIGHT", 5);
 define("LEFT_MARGIN", 10);

 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 class WikiConverterPDF
 {
 	var $root_it, $wiki_it, $parser, $pdf;
 	
 	function setObjectIt( $wiki_it )
 	{
		$editor = WikiEditorBuilder::build($wiki_it->get('ContentEditor'));

		$editor->setObjectIt( $wiki_it );

 		$this->parser = $editor->getHtmlParser();
 		
 		$this->parser->setObjectIt( $wiki_it ); 
 		
 		$this->parser->setRequiredExternalAccess();

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
 		
 		$this->parser->setRequiredExternalAccess();
 	}

 	function setRevision( $change_it )
 	{
 		$this->change_it = $change_it;
 	}
 	
 	function parse()
 	{
 		global $model_factory;
	
		$this->pdf = new FPDF();
		$this->pdf->AddFont(FONT_FAMILY, '', 'times.php');
		$this->pdf->AddFont(FONT_FAMILY, 'B', 'timesbd.php');
		$this->pdf->AddFont(FONT_FAMILY, 'BI', 'timesbi.php');
		$this->pdf->AddFont(FONT_FAMILY, 'I', 'timesi.php');

		$object_it = $this->getObjectIt();
		
		$this->root_it = $object_it->copy();
		
		while ( !$object_it->end() )
		{
			$this->pdf->AddPage();
			
			$this->transformWiki( $object_it );
			
			$object_it->moveNext();
		}
		
		$this->display();
 	}

	function transformWiki( $parent_it )
	{
		$this->pdf->SetFont(FONT_FAMILY, '', FONT_HEADER0);

		$this->pdf->Cell( 40, LEFT_MARGIN, html_entity_decode($parent_it->getDisplayName(), ENT_QUOTES | ENT_HTML401, APP_ENCODING) );
        $this->pdf->Ln();

		if ( is_object($this->change_it) )
		{
			$content = $this->change_it->getHtmlDecoded('Content');
		}
		else
		{
			$content = $parent_it->getHtmlDecoded('Content');
		}
		
		$content = preg_replace("/[\&\#\?]{1}[a-z0-9]{2,8};/i", "", $content);
		
		$content = $this->parser->parse( $content );

		$this->transform( $content );
		
		if ( $content != '' )
		{	
			$this->pdf->Ln(LINE_HEIGHT);
		}
	}

	function transform( &$html )
	{
		global $wiki_converter;
		$wiki_converter = $this;

        $this->pdf->SetFont(FONT_FAMILY, '', FONT_NORMAL);

		$tag = preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE);

        $this->tables = array();
		foreach( $tag as $tindex => $tvalue )
		{
			if( $tindex % 2 == 0 )
			{
				switch ( $this->current_tag )
				{
					case 'TABLE':
						array_push( $this->tables, array( 'rows' => 0, 'cols' => 0 ) );
						break;

					case 'TR':
						$this->tables[count($this->tables) - 1]['rows']++;
						break;

					case 'TD':
						$this->tables[count($this->tables) - 1]['cols']++;
						break;
				}
			}
			else
			{
				//Tag
				if( $tvalue[0] == '/')
				{
					$this->current_tag = '';
				}
				else
				{
					//Extract attributes
					$a2=explode(' ',$tvalue);
					$tag_name = trim(strtoupper(array_shift($a2)), '/');
					$this->current_tag = $tag_name;
				}
			}
		}
		
        $this->current_table = 0;
		foreach( $tag as $index => $value )
		{
			if( $index % 2 == 0 )
			{
				//Text
				if( $this->HREF )
				{
					$this->PutLink($this->HREF, $value);
				}
				else
				{
					if ( preg_match('/h[\d]{1}/i', $this->current_tag) )
					{
						$value = trim($value, ' ');
					}

					switch ( $this->current_tag )
					{
						case 'TD':
							$columns = $this->tables[$this->current_table - 1]['cols'] /
								$this->tables[$this->current_table - 1]['rows'];
								
							$this->pdf->Cell( 190 / $columns, LINE_HEIGHT, trim($value, ' '), 'LRTB', 0, 'J');
							break;
						
						case 'LI':
							$this->pdf->MultiCell( 0, LINE_HEIGHT, $value, 0, 'L');
							break;

						case 'PRE':
							$this->pdf->MultiCell( 0, LINE_HEIGHT, $value, 'LRTB', 'L');
							break;

						case 'DIV':
							if ( $this->CLASS == 'wiki_note' || $this->CLASS == 'important' )
							{
								$this->pdf->MultiCell( 0, LINE_HEIGHT, $value, 'LRTB', 'L');
							}
							else
							{
								$this->pdf->Write(LINE_HEIGHT, $value);
							}
							break;

						case 'IMG':
							
							$file_it = $this->parser->getFileByHref( $this->image_source );
							if ( is_object($file_it) && $file_it->count() > 0 )
							{
								$info = pathinfo($file_it->getFileName( 'Content' ));
								$path = SERVER_FILES_PATH.$file_it->object->getClassName().
												'/'.basename($file_it->getFilePath( 'Content' ));
	
								if ( file_exists($path) )
								{
									if ( strpos($this->image_width, '%') > 0 )
									{
										$target_width = 190 * ( trim($this->image_width, '%') / 100 );
									}
									else
									{
										$target_width = $this->image_width * 0.24;
									}
									
									list ($width, $height) = getimagesize($path);
									
									$scale = $width / $height;
									$target_height = round($target_width / $scale, 0); 
									
									$this->pdf->Image( $path, null, null, $target_width, $target_height, 'png' );
								}
							}

							break;
							
						default:
							$this->pdf->Write(LINE_HEIGHT, $value);
					}
					
					$this->current_tag = '';
				}
			}
			else
			{
				//Tag
				if( $value[0] == '/')
					$this->CloseTag(strtoupper(substr($value,1)));
				else
				{
					//Extract attributes
					$a2 = explode(' ',$value);
					$tag_name = trim(strtoupper(array_shift($a2)), '/');
					$attr=array();
					foreach($a2 as $v)
					{
						if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
							$attr[strtoupper($a3[1])]=$a3[2];
					}
					$this->OpenTag($tag_name,$attr);
				}
			}
		}

        $this->pdf->Ln();
	}
	
	function OpenTag($tag, $attr)
	{
		//Opening tag
		switch ( $tag )
		{
			case 'B':
			case 'I':
			case 'U':
				$this->SetStyle($tag, true);
				break;

			case 'H1':
			case 'H2':
			case 'H3':
			case 'H4':
			case 'H5':
			case 'H6':
				$this->pdf->Ln(LINE_HEIGHT*1.5);
				$this->SetStyle($tag, true);
				break;
				
			case 'A':
				$this->HREF=$attr['HREF'];
				break;
				
			case 'DIV':
				$this->CLASS=$attr['CLASS'];
				$this->pdf->Ln(LINE_HEIGHT);
				break;

			case 'UL':
				$this->LI_MARGIN += LEFT_MARGIN;
				$this->LI_MARKER = '';
				break;

			case 'OL':
				$this->LI_MARKER = 1;
				$this->LI_MARGIN += LEFT_MARGIN;
				break;

			case 'LI':
				$this->pdf->Cell( $this->LI_MARGIN, LINE_HEIGHT, 
					!is_numeric($this->LI_MARKER) ? chr(149) : $this->LI_MARKER.' ', '', 0, 'R');

				if ( is_numeric($this->LI_MARKER) )
				{
					$this->LI_MARKER++;
				}
				break;

			case 'BR':
				$this->pdf->Ln(LINE_HEIGHT);
				break;

			case 'IMG':
				$this->image_source = $attr['SRC'];
				
				$this->image_width = $attr['WIDTH'];
				if ( $this->image_width == '' )
				{
					$this->image_width = '100%';
				}
				break;
				
			case 'TABLE':
				$this->current_table++;
				$this->current_column = 0;
				break;

			case 'TR':
				$this->current_column = 0;
				break;

			case 'TD':
				$this->current_column++;
				break;
		}
		
		$this->current_tag = $tag;
		$this->was_tag = '';
	}
	
	function CloseTag($tag)
	{
		//Closing tag
		switch ( $tag )
		{
			case 'B':
			case 'I':
			case 'U':
				$this->SetStyle($tag, false);
				break;

			case 'H1':
			case 'H2':
			case 'H3':
				$this->SetStyle($tag, false);
				$this->pdf->Ln(LINE_HEIGHT + 3);
				break;

			case 'H4':
			case 'H5':
			case 'H6':
				$this->SetStyle($tag, false);
				$this->pdf->Ln(LINE_HEIGHT + 1);
				break;

			case 'A':
				$this->HREF='';
				break;

			case 'UL':
			case 'OL':
				$this->LI_MARGIN -= LEFT_MARGIN;
				break;

			case 'LI':
				$this->pdf->Ln(3);
				break;

			case 'TR':
				$this->pdf->Ln();
				break;
		}

		$this->was_tag = $this->current_tag;
		$this->current_tag = '';
	}
	
	function SetStyle( $tag, $enable )
	{
		if ( !$enable )
		{
			$this->pdf->SetFont(FONT_FAMILY, '', FONT_NORMAL);
			return;
		}
		
		switch ( $tag )
		{
			case 'B':
			case 'I':
			case 'U':
				$this->pdf->SetFont('', $tag);
				break;

			case 'H1':
				$this->pdf->SetFont('', '', FONT_HEADER1);
				break;
				
			case 'H2':
				$this->pdf->SetFont('', 'B', FONT_HEADER2);
				break;

			case 'H3':
				$this->pdf->SetFont('', 'B', FONT_HEADER3);
				break;

			case 'H4':
				$this->pdf->SetFont('', 'B', FONT_HEADER4);
				break;

			case 'H5':
				$this->pdf->SetFont('', 'B', FONT_HEADER5);
				break;

			case 'H6':
				$this->pdf->SetFont('', 'B', FONT_HEADER6);
				break;
		}
	}
	
	function PutLink($URL,$txt)
	{
		//Put a hyperlink
		$this->pdf->SetTextColor(0,0,255);
		$this->SetStyle('U',true);
		$this->pdf->Write(LINE_HEIGHT,$txt,$URL);
		$this->SetStyle('U',false);
		$this->pdf->SetTextColor(0);
	}
		
 	function display()
 	{
		$file_name = preg_replace('/[\.\,\+\)\(\)\:\;]/i', '_', 
			html_entity_decode($this->root_it->getDisplayName(), ENT_QUOTES | ENT_HTML401, APP_ENCODING)).'.pdf';

 		if ( EnvironmentSettings::getBrowserPostUnicode() )
		{ 
			$file_name = IteratorBase::wintoutf8($file_name);
		}
		
		$this->pdf->Output($file_name, 'D');
 	}
 }

?>