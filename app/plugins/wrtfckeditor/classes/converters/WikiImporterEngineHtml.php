<?php
include_once SERVER_ROOT_PATH . "pm/classes/wiki/converters/WikiImporterEngine.php";

class WikiImporterEngineHtml extends WikiImporterEngine
{
    protected function getHtml( $filePath )
    {
        try {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            if ( strpos($finfo->file($filePath), 'html') === false ) return "";

            return TextUtils::checkHtml(file_get_contents($filePath));
        }
        catch( Exception $e ) {
            \Logger::getLogger('System')->error($e->getMessage().$e->getTraceAsString());
        }
        return "";
    }
}