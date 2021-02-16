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
 				return 10;
            default:
                $type = $this->getIterator()->object->getAttributeType($field);
                switch( $type ) {
                    case 'wysiwyg':
                        return 100;
                }
 		}
	}
	
 	function getRowStyle( $object_it )
 	{
 		return '';
 	}
 	
 	function getDescription()
 	{
 		return '';
 	}

 	function getFormula( $row, $columnIndex, $cellName )
 	{
 		return '';
 	}

 	protected function sanitizeWorkSheetName( $name ) {
 		return htmlspecialchars(preg_replace('/[\/\\\*\?\[\]]+/', '', mb_substr($name, 0, 31)));
 	}

    function worksheet()
    {
        $objPHPExcel = new PHPExcel();

        $userIt = getSession()->getUserIt();
        $objPHPExcel->getProperties()
            ->setCreator($userIt->getDisplayName())
            ->setLastModifiedBy($userIt->getDisplayName())
            ->setTitle($this->getName())
            ->setSubject($this->getName())
            ->setKeywords("devprom");

        $fields = $this->getFields();
        $it = $this->getIterator();
        $sheet = $objPHPExcel->setActiveSheetIndex(0);

        foreach( array_keys($fields) as $key => $fieldName )
        {
            $black = new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_BLACK);
            $sheet->setCellValueByColumnAndRow($key, 1, $fields[$fieldName])
                ->getStyleByColumnAndRow($key, 1)
                    ->getFill()
                    ->setStartColor($black)
                    ->setEndColor($black)
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $sheet->getStyleByColumnAndRow($key, 1)
                    ->getFont()
                    ->getColor()
                    ->setARGB(PHPExcel_Style_Color::COLOR_WHITE);

            if ( in_array($it->object->getAttributeType($fieldName), array('date','datetime')) ) {
                $sheet->getStyleByColumnAndRow($key, 1)
                    ->getNumberFormat()
                    ->setFormatCode(getSession()->getLanguage()->getExcelDateFormat());
            }
        }

        $this->parser = WikiEditorBuilder::build()->getHtmlParser();

        while( !$it->end() ) {
            $row = $it->getPos() + 2;
            foreach( array_keys($fields) as $key => $fieldName )
            {
                $formula = $this->getFormula($row, $key, PHPExcel_Cell::stringFromColumnIndex($key));
                if ( $formula != '' ) {
                    $sheet->setCellValueByColumnAndRow($key, $row, $formula);
                    continue;
                }

                list( $value, $type ) = $this->getValue( $fieldName, $it );
                if ( $type != '' ) {
                    if ( $type == 'DateTime' ) {
                        $sheet->getStyleByColumnAndRow($key, $row)
                            ->getNumberFormat()
                            ->setFormatCode(getSession()->getLanguage()->getExcelDateTimeFormat());
                        if ( $value != '' ) {
                            $sheet->setCellValueByColumnAndRow($key, $row, PHPExcel_Shared_Date::PHPToExcel($value));
                        }
                    }
                    elseif ( $type == 'Date' ) {
                        $sheet->getStyleByColumnAndRow($key, $row)
                            ->getNumberFormat()
                            ->setFormatCode(getSession()->getLanguage()->getExcelDateFormat());
                        if ( $value != '' ) {
                            $sheet->setCellValueByColumnAndRow($key, $row, PHPExcel_Shared_Date::PHPToExcel( $value ));
                        }
                    }
                    else {
                        $sheet->setCellValueExplicitByColumnAndRow($key, $row, $value, $type);
                        if ( $fieldName == 'Caption' && $it->get('ParentPath') != '' ) {
                            $sheet->getStyleByColumnAndRow($key, $row)
                                ->getAlignment()->setIndent((count(explode(',', $it->get('ParentPath'))) - 3)*2 );
                        }
                    }
                }
                else {
                    $sheet->setCellValueByColumnAndRow($key, $row, $value);
                }

                $comment = $this->comment($fieldName);
                if ( $comment != "" ) {
                    $sheet->getCommentByColumnAndRow($key, $row)
                        ->setText($comment);
                }

                $sheet->getStyleByColumnAndRow($key, $row)
                    ->getAlignment()
                    ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)
                    ->setWrapText(true);
            }
            $it->moveNext();
        }

        foreach( array_keys($fields) as $key => $fieldName )
        {
            $columnWidth = $this->getWidth($fieldName);
            if ( $columnWidth != '' ) {
                $sheet->getColumnDimensionByColumn($key)->setWidth($columnWidth);
            }
            else {
                $sheet->getColumnDimensionByColumn($key)->setAutoSize(true);
            }
        }
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save("php://output");
    }

 	function getFields()
    {
        $fields = parent::getFields();

        foreach( array('StateDuration','LeadTime') as $field ) {
            if ( array_key_exists($field, $fields) ) {
                $fields[$field] .= ', '.translate('ч.');
            }
        }

        $options = array_filter($this->getOptions(), function($item) {
            return strpos($item, 'extraFields') !== false;
        });
        $extraFieldsOption = array_shift($options);
        $extraFields = preg_split('/:/', $extraFieldsOption);
        array_shift($extraFields);

        $object = $this->getIterator()->object;
        foreach( $extraFields as $field ) {
            $title = translate($object->getAttributeUserName($field));
            if ( $field == 'SectionNumber' ) {
                $fields = array_merge(
                    array( $field => $title ),
                    $fields
                );
            }
            else {
                $fields[$field] = $title;
            }
        }

        return $fields;
    }

 	function getValue( $key, $iterator )
 	{
 	    if ( !$iterator->object instanceof Metaobject ) return "";
 		$type = $iterator->object->getAttributeType( $key );

        switch( $key )
        {
            case 'UID':
                $uid = new ObjectUID;
                return array(
                    $iterator->object->IsAttributeStored($key)
                        ? $iterator->get($key)
                        : $uid->getObjectUid( $iterator->getCurrentIt() ),
                    PHPExcel_Cell_DataType::TYPE_STRING
                );

            case 'StateDuration':
            case 'LeadTime':
                return array($iterator->get($key), PHPExcel_Cell_DataType::TYPE_NUMERIC);
        }

        switch ( strtolower($type) )
        {
            case 'integer':
            case 'float':
                $value = $this->get( $key );
                $type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
                break;
            case 'date':
                $type = "Date";
                $value = strtotime(SystemDateTime::convertToClientTime($iterator->get($key)));
                break;
            case 'datetime':
                $type = "DateTime";
                $value = strtotime(SystemDateTime::convertToClientTime($iterator->get($key)));
                break;
            case 'wysiwyg':
                $this->parser->setObjectIt($iterator->copy());
                $content = $this->parser->parse($iterator->getHtmlDecoded($key));
                $html2text = new \Html2Text\Html2Text($content, array('width'=>0));
                $value = $html2text->getText();
                $type = PHPExcel_Cell_DataType::TYPE_STRING;
                break;
            default:
                $value = $this->get($key);
                if ( is_array($value) ) {
                    $self = $this;
                    $value = join(chr(10), array_map(
                            function($value) use($self) {
                                return $value;
                            }, $value)
                    );
                }
                if ( is_numeric($value) ) {
                    $type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
                }
                else {
                    $type = PHPExcel_Cell_DataType::TYPE_STRING;
                }
        }
 		return array( $value, $type );
 	}

 	function export()
 	{

	 	header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-control: no-store");
		header('Content-Type: application/vnd.ms-excel');
		header(EnvironmentSettings::getDownloadHeader($this->getName().'.xls'));

		echo $this->worksheet();
 	}
}