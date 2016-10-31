<?php
include_once "IteratorExport.php";

class IteratorExportExcel extends IteratorExport
{
	function IteratorExportExcel( $iterator )
	{
		parent::IteratorExport( $iterator );
	}
	
	function getWidth( $field )
	{
 		switch ( $field )
 		{
 			case 'UID':
 				return 40;
 				
 			default:
 				return 200;
 		}
	}
	
 	function getRowStyle( $object_it )
 	{
 		return '';
 	}
 	
 	function getFieldsStyle()
 	{
 		return 's21';
 	}

 	function getDescription()
 	{
 		return '';
 	}

 	function getFormula( $row, $cell )
 	{
 		return '';
 	}

 	function workbook()
 	{
 		$result = '<?xml version="1.0" encoding="'.APP_ENCODING.'"?>'.
			'<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">';
 		
 		$result .= $this->properties();
 		
 		$result .= $this->styles();
 		
 		$result .= $this->worksheet();

 		$result .= '</Workbook>';
 		
 		return $result;
 	}
 	
 	function properties()
 	{
 		$user_it = getSession()->getUserIt();
 		
 		$result = '<DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">';

		if ( is_object($user_it) )
		{
			$attributes = array (
				'Author' => $user_it->getDisplayName(),
				'LastAuthor' => $user_it->getDisplayName(),
				'Created' => ''
				);
	
			$result .= $this->convert($attributes);
			}
		
  		$result .= '</DocumentProperties>';
  		
  		return $result;
 	}
 	
 	function styles()
 	{
 		$result = '<Styles><Style ss:ID="Default" ss:Name="Normal">';
 		
		$result .= '<Alignment ss:Vertical="Top" ss:WrapText="1"/>' .
				'<Borders/>' .
				'<Font ss:FontName="Arial"/>' .
				'<Interior/>' .
				'<NumberFormat/>' .
				'<Protection/>' .
				'</Style>';
 		
 		$result .= '<Style ss:ID="s21">'.
  			'<Font ss:FontName="Arial Cyr" ss:Color="#FFFFFF"/>'.
  			'<Interior ss:Color="#000000" ss:Pattern="Solid" />'. 
  			'</Style>';

 		$result .= '<Style ss:ID="s22">'.
  			'<Font ss:FontName="Arial Cyr" ss:Bold="1" />'.
  			'<Interior ss:Color="#C0C0C0" ss:Pattern="Solid" />'. 
  			'</Style>';
  			
 		$result .= '<Style ss:ID="s23">'.
  			'<Alignment ss:Vertical="Top" ss:WrapText="1"/>'. 
  			'</Style>';

 		$result .= '<Style ss:ID="s24">'.
  			'<NumberFormat ss:Format="yyyy\-mm\-dd"/>'. 
  			'</Style>';
 		
 		$result .= '</Styles>';
 		
 		return $result;
 	}
 	 
 	protected function sanitizeWorkSheetName( $name ) {
 		return htmlspecialchars(preg_replace('/[\/\\\*\?\[\]]+/', '', mb_substr($name, 0, 31)));
 	}
 	
 	function worksheet()
 	{
 		$fields = $this->getFields();
 		$fieldsstyle = $this->getFieldsStyle();
 		
 		$keys = array_keys($fields);
 		
 		$result = '<Worksheet ss:Name="'.$this->sanitizeWorkSheetName($this->getName()).'">' .
 			'<Table ss:ExpandedColumnCount="'.count($keys).
		    '" ss:ExpandedRowCount="'.($this->count()+1).'" x:FullColumns="1" x:FullRows="1">';

		for ( $j = 0; $j < count($keys); $j++ )
		{
			$result .= '<Column ss:AutoFitWidth="0" '.
					' ss:Width="'.$this->getWidth($keys[$j]).'"/>';
		}

		$result .= '<Row '.($fieldsstyle != '' ? 'ss:StyleID="'.$fieldsstyle.'"' : '').'>';

		for ( $j = 0; $j < count($keys); $j++ )
		{
			$type = 'String';
			
			if ( is_numeric($fields[$keys[$j]]) )
			{
				$type = 'Number';
			}

			$result .= '<Cell>';
			
			$result .= '<Data ss:Type="'.$type.'">'.
				$fields[$keys[$j]].'</Data>';
				
			$result .= '</Cell>';
		}
		
		$result .= '</Row>';

		$it = $this->getIterator();
		
		$i = 0;
		
 		while( !$it->end() )
 		{
			$style = $this->getRowStyle($this->it);
			
			if ( $style != '' ) 
			{
 				$result .= '<Row ss:StyleID="'.$style.'">';
			}
			else
			{
 				$result .= '<Row ss:StyleID="s23">';
			}

 			for ( $j = 0; $j < count($keys); $j++ )
 			{
 				list( $value, $type ) = $this->getValue( $keys[$j], $it );
 				
 				switch( $type )
 				{
 					case 'DateTime':
 						$style = 'ss:StyleID="s24"';
 						break;
 						
 					default:
 						$style = '';
 						break;
 				}
 				
 				$formula = $this->getFormula($i, $j);
 				
 				$result .= '<Cell '.$style.' '.($formula != '' ? 'ss:Formula="='.$formula.'"' : '').'>';
 				$result .= '<Data ss:Type="'.$type.'">'.$value.'</Data>';
 					
 				$comment = $this->comment($keys[$j]);
 				if ( $comment != '' )
 				{
	 				$result .= '<Comment><Data><![CDATA['.
	 					html_entity_decode($comment, ENT_COMPAT | ENT_HTML401, APP_ENCODING).']]></Data></Comment>';
 				}
 				
				$result .= '</Cell>';
 			}
 			
 			$result .= '</Row>';

 			$it->moveNext();
 		}

 		$result .= '</Table>' .
 			'</Worksheet>';
 			
 		return $result;
 	}

    function getFields()
    {
        $fields = parent::getFields();

        foreach( array('StateDuration','LeadTime') as $field ) {
            if ( array_key_exists($field, $fields) ) {
                $fields[$field] .= ', '.translate('ч.');
            }
        }

        return $fields;
    }

 	function getValue( $key, $iterator )
 	{
 		$type = $iterator->object->getAttributeType( $key );

        switch( $key )
        {
            case 'UID':
                $uid = new ObjectUID;
                return array( $uid->getObjectUid( $iterator->getCurrentIt() ), "String" );

            case 'StateDuration':
            case 'LeadTime':
                return array($iterator->get($key), 'Number');
        }

 		if ( !$iterator->object->IsReference( $key ) )
 		{
	 		switch ( strtolower($type) )
	 		{
	 			case 'integer':
	 			case 'float':
	 				$value = $this->get( $key );
	 				$type = "Number";
	 				break;
	 				 
	 			case 'datetime':
	 				$type = "DateTime";
	 				$value = $iterator->getDateFormatUser( $key, '%Y-%m-%dT00:00:00.000' );
	 				break;
	 		}
 		}

 		if ( $value == '' )
 		{
 		    $value = $this->get($key);
 		    
 		    if ( is_array($value) ) {
 		        $self = $this;
 		        $value = join(chr(10), array_map(
 		            function($value) use($self) {
 		                return $self->decode($value);
                    }, $value)
                );
            }
            else {
                $value = $this->decode($value);
            }
 		    
 			if ( is_numeric($value) ) {
		 		$type = "Number";
 			}
 			else {
		 		$value = '<![CDATA['.addslashes(TextUtils::getXmlString($value)).']]>';
		 		$type = "String";
 			}
 		}
 		
 		return array( $value, $type );
 	}

 	function convert ( $attributes )
 	{
		$tags = array_keys($attributes);
		for ( $i = 0; $i < count($tags); $i++ )
		{
			$result .= '<'.$tags[$i].'>'.$attributes[$tags[$i]].'</'.$tags[$i].'>';
		}
		
		return $result;
 	}
 	
 	function export()
 	{
	 	header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-control: no-store");
		header('Content-Type: application/vnd.ms-excel');
		header(EnvironmentSettings::getDownloadHeader($this->getName().'.xls'));
		
		echo $this->workbook();
 	}
}