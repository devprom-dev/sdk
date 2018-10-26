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
 				return -1;
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

        $objPHPExcel->setActiveSheetIndex(0)
            ->getDefaultRowDimension()
            ->setRowHeight();

        $fields = $this->getFields();
        foreach( array_keys($fields) as $key => $fieldName )
        {
            $black = new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_BLACK);
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($key, 1, $fields[$fieldName])
                ->getStyleByColumnAndRow($key, 1)
                    ->getFill()
                    ->setStartColor($black)
                    ->setEndColor($black)
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->setActiveSheetIndex(0)
                ->getStyleByColumnAndRow($key, 1)
                    ->getFont()
                    ->getColor()
                    ->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
            $objPHPExcel->setActiveSheetIndex(0)
                ->getColumnDimensionByColumn($key)
                ->setWidth( $this->getWidth($fieldName) );
        }

        $it = $this->getIterator();
        while( !$it->end() ) {
            $row = $it->getPos() + 2;
            foreach( array_keys($fields) as $key => $fieldName )
            {
                $formula = $this->getFormula($row, $key, PHPExcel_Cell::stringFromColumnIndex($key));
                if ( $formula != '' ) {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($key, $row, $formula);
                    continue;
                }

                list( $value, $type ) = $this->getValue( $fieldName, $it );
                if ( $type != '' ) {
                    if ( $type == 'DateTime' ) {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValueByColumnAndRow($key, $row, PHPExcel_Shared_Date::PHPToExcel( $value ));
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->getStyleByColumnAndRow($key, $row)
                            ->getNumberFormat()
                            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME);
                    }
                    elseif ( $type == 'Date' ) {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValueByColumnAndRow($key, $row, PHPExcel_Shared_Date::PHPToExcel( $value ));
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->getStyleByColumnAndRow($key, $row)
                            ->getNumberFormat()
                            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
                    }
                    else {
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValueExplicitByColumnAndRow($key, $row, $value, $type);
                    }
                }
                else {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($key, $row, $value);
                }

                $comment = $this->comment($fieldName);
                if ( $comment != "" ) {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->getCommentByColumnAndRow($key, $row)
                        ->setText($comment);
                }

                $objPHPExcel->setActiveSheetIndex(0)
                    ->getStyleByColumnAndRow($key, $row)
                        ->getAlignment()
                        ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)
                        ->setWrapText(true);
            }
            $it->moveNext();
        }

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