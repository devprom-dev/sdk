<?php
include_once SERVER_ROOT_PATH . "pm/classes/wiki/converters/WikiImporterEngine.php";

class WikiImporterEngineLibreOffice extends WikiImporterEngine
{
    protected function getHtml( $filePath )
    {
        $outputPath = SERVER_FILES_PATH . 'tmp/' . md5(uniqid('libreofficeoutput'));
        mkdir($outputPath, 0777, true);

        $command = ' --headless --convert-to "html:XHTML Writer File:UTF8" --outdir "'.$outputPath.'" "'.$filePath.'" 2>&1';
        Logger::getLogger('Commands')->info(get_class($this).': '.$command);

        try {
            \FileSystem::execLibreOffice($command);
        }
        catch( \Exception $e ) {
            Logger::getLogger('Commands')->error(get_class($this).': '.$e->getMessage());
            throw $e;
        }

        $info = pathinfo($filePath);
        $fileName = $outputPath . '/' . $info['filename'] . '.html';
        if ( !file_exists($fileName) ) return '';

        $content = file_get_contents($fileName);
        $content = preg_replace_callback( '/<img\s+([^>]*)>/i', array('HtmlImageConverter', 'replaceExternalImageCallback'), $content);
        $content = preg_replace_callback( '/<embed\s+([^>]*)>/i', array('HtmlImageConverter', 'replaceExternalImageCallback'), $content);
        $content = preg_replace('/<embed/i', '<img', $content);

        // append table borders
        $content = preg_replace('/<table>/i', '<table border="1">', $content);
        $content = preg_replace_callback('/<table([^>]*)>/i', function($match) {
            return str_replace('border="0"', 'border="1"', $match[0]);
        }, $content);

        FileSystem::rmdirr($outputPath);

        return \TextUtils::getValidHtml(
                    \TextUtils::getCleansedHtml(
                        \TextUtils::getUnstyledHtml($content)
                    )
                );
    }
}