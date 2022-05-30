<?php
use \PhpOffice\PhpSpreadsheet\IOFactory;
use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Cell;
use \PhpOffice\PhpSpreadsheet\Style;
use \PhpOffice\PhpSpreadsheet\Shared;
include_once "IteratorExport.php";

class IteratorExportExcel extends IteratorExport
{
	function __construct( $iterator )
	{
		parent::__construct( $iterator );

        PhpOffice\PhpSpreadsheet\Shared\Date::setDefaultTimezone(
            \EnvironmentSettings::getClientTimeZone());
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
        $objPHPExcel = new Spreadsheet();

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
            $column = $key + 1;
            $sheet->setCellValueByColumnAndRow($column, 1, $fields[$fieldName])
                ->getStyleByColumnAndRow($column, 1)
                    ->getFill()
                    ->setFillType(Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB(Style\Color::COLOR_BLACK);
            $sheet->getStyleByColumnAndRow($column, 1)
                    ->getFont()
                    ->getColor()
                    ->setARGB(Style\Color::COLOR_WHITE);

            if ( in_array($it->object->getAttributeType($fieldName), array('date','datetime')) ) {
                $sheet->getStyleByColumnAndRow($column, 1)
                    ->getNumberFormat()
                    ->setFormatCode(getSession()->getLanguage()->getExcelDateFormat());
            }
        }

        $this->parser = WikiEditorBuilder::build()->getHtmlParser();

        while( !$it->end() ) {
            $row = $it->getPos() + 2;
            foreach( array_keys($fields) as $key => $fieldName )
            {
                $column = $key + 1;
                $formula = $this->getFormula($row, $column, Cell\Coordinate::stringFromColumnIndex($column));
                if ( $formula != '' ) {
                    $sheet->setCellValueByColumnAndRow($column, $row, $formula);
                    continue;
                }

                list( $value, $type ) = $this->getValue( $fieldName, $it );
                if ( $type != '' ) {
                    if ( $type == 'DateTime' ) {
                        $sheet->getStyleByColumnAndRow($column, $row)
                            ->getNumberFormat()
                            ->setFormatCode(getSession()->getLanguage()->getExcelDateTimeFormat());
                        if ( $value != '' ) {
                            $sheet->setCellValueByColumnAndRow($column, $row, Shared\Date::PHPToExcel($value));
                        }
                    }
                    elseif ( $type == 'Date' ) {
                        $sheet->getStyleByColumnAndRow($column, $row)
                            ->getNumberFormat()
                            ->setFormatCode(getSession()->getLanguage()->getExcelDateFormat());
                        if ( $value != '' ) {
                            $sheet->setCellValueByColumnAndRow($column, $row, Shared\Date::PHPToExcel( $value ));
                        }
                    }
                    else {
                        $sheet->setCellValueExplicitByColumnAndRow($column, $row, $value, $type);
                        if ( $fieldName == 'Caption' && $it->get('ParentPath') != '' ) {
                            $sheet->getStyleByColumnAndRow($column, $row)
                                ->getAlignment()->setIndent((count(explode(',', $it->get('ParentPath'))) - 3)*2 );
                        }
                    }
                }
                else {
                    $sheet->setCellValueByColumnAndRow($column, $row, $value);
                }

                $comment = $this->comment($fieldName);
                if ( $comment != "" ) {
                    $sheet->getCommentByColumnAndRow($column, $row)
                        ->setText($comment);
                }

                $sheet->getStyleByColumnAndRow($column, $row)
                    ->getAlignment()
                    ->setVertical(Style\Alignment::VERTICAL_TOP)
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

        $objWriter = IOFactory::createWriter($objPHPExcel,
            defined('UI_EXTENSION') && !UI_EXTENSION ? 'Xls' : 'Xlsx');
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
                    Cell\DataType::TYPE_STRING
                );

            case 'StateDuration':
            case 'LeadTime':
                return array($iterator->get($key), Cell\DataType::TYPE_NUMERIC);
        }

        switch ( strtolower($type) )
        {
            case 'integer':
            case 'float':
                $value = $this->get( $key );
                $type = Cell\DataType::TYPE_NUMERIC;
                break;
            case 'date':
                $type = "Date";
                $value = $iterator->get($key);
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
                $type = Cell\DataType::TYPE_STRING;
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
                    $type = Cell\DataType::TYPE_NUMERIC;
                }
                else {
                    $type = Cell\DataType::TYPE_STRING;
                }
        }
 		return array( $value, $type );
 	}

 	function export()
 	{
        $ext = defined('UI_EXTENSION') && !UI_EXTENSION ? 'xls' : 'xlsx';
	 	header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-control: no-store");
		header('Content-Type: application/vnd.ms-excel');
		header(EnvironmentSettings::getDownloadHeader("{$this->getName()}.{$ext}"));

		echo $this->worksheet();
 	}
}