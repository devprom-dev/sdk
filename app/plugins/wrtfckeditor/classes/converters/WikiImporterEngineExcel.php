<?php
use \PhpOffice\PhpSpreadsheet\IOFactory;
include_once SERVER_ROOT_PATH . "pm/classes/wiki/converters/WikiImporterEngine.php";

class WikiImporterEngineExcel extends WikiImporterEngine
{
    protected function getHtml( $filePath )
    {
        $content = '';

        try {
            // detects type of the file
            $supportedFormat = false;
            foreach (array('Xlsx','Xls','Xml','Ods','Csv') as $type) {
                try {
                    $reader = IOFactory::createReader($type);
                    if ($reader->canRead($filePath)) {
                        $supportedFormat = true;
                        break;
                    }
                }
                catch(\Exception $e) {
                }
            }
            if ( !$supportedFormat ) return $content;

            $objPHPExcel = IOFactory::load($filePath);
            foreach( $objPHPExcel->getAllSheets() as $index => $sheet )
            {
                $objWriter = IOFactory::createWriter($objPHPExcel, 'Html');
                $objWriter->setSheetIndex($index);

                ob_start();
                $objWriter->save("php://output");
                $pageContent = ob_get_contents();
                ob_end_clean();

                // append table borders
                $pageContent = preg_replace('/<table border="0"/i', '<table border="1"', $pageContent);

                if ( $pageContent != '' ) {
                    $content .= '<h1>'.$sheet->getTitle().'</h1>'.$pageContent;
                }
            }
        }
        catch( \Exception $e ) {
            \Logger::getLogger('System')->error($e->getMessage().$e->getTraceAsString());
        }

        return $content;
    }
}