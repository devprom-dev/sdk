<?php
include_once SERVER_ROOT_PATH . "pm/classes/wiki/converters/WikiImporterEngine.php";
include_once SERVER_ROOT_PATH . "ext/mhtml/MhtToHtml.php";

class WikiImporterEngineMhtml extends WikiImporterEngine
{
    protected function getHtml( $filePath )
    {
        $content = '';

        try {
            // detects type of the file
            $fileHandle = fopen($filePath, 'r');
            $fileHeader = fread($fileHandle, 254);
            fclose($fileHandle);
            if ( strpos($fileHeader, 'multipart/related; boundary="') === false ) return $content;

            //$reader = new MhtToHtml($filePath, SERVER_FILES_PATH . 'tmp');
            //$reader->parse();
        }
        catch( Exception $e ) {
            \Logger::getLogger('System')->error($e->getMessage().$e->getTraceAsString());
        }

        return $content;
    }
}