<?php
include_once SERVER_ROOT_PATH . "pm/classes/wiki/converters/WikiImporterEngine.php";

class WikiImporterEnginePanDoc extends WikiImporterEngine
{
    protected function getHtml( $filePath )
    {
        $outputPath = str_replace("\\", "/", realpath(tempnam(SERVER_FILES_PATH, "pandocoutput")));

        $command = 'pandoc --to=html --data-dir="'.SERVER_FILES_PATH.'" --extract-media="'.trim(SERVER_FILES_PATH,'\\/').'" -o "'.$outputPath.'" "'.$filePath.'" 2>&1';
        Logger::getLogger('Commands')->info(get_class($this).': '.$command);

        $result = shell_exec($command);

        $lines = explode(PHP_EOL, $result);
        foreach( $lines as $key => $line ) {
            if ( strpos($line, 'extracting') ) unset($lines[$key]);
        }
        $result =join(PHP_EOL, $lines);

        if ( $result != "" ) {
            Logger::getLogger('Commands')->error(get_class($this).': '.$result);
            throw new Exception($result);
        }

        $content = file_get_contents($outputPath);
        $content = preg_replace_callback( '/<img\s+([^>]*)>/i', array('HtmlImageConverter', 'replaceExternalImageCallback'), $content);

        // append table borders
        $content = preg_replace('/<table>/i', '<table border="1">', $content);

        @unlink($outputPath);
        FileSystem::rmdirr(SERVER_FILES_PATH.'media');

        return $content;
    }
}