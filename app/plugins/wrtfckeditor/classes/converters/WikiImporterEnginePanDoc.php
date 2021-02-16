<?php
include_once SERVER_ROOT_PATH . "pm/classes/wiki/converters/WikiImporterEngine.php";

class WikiImporterEnginePanDoc extends WikiImporterEngine
{
    protected function getHtml( $filePath )
    {
        $outputPath = str_replace("\\", "/", realpath(tempnam(SERVER_FILES_PATH, "pandocoutput")));

        $pandocVersion = $this->getVersion();
        Logger::getLogger('Commands')->info('pandoc version is '.$pandocVersion);

        $requiredParms = '--to=html';
        if ( defined('PANDOC_RTS') && PANDOC_RTS != '' ) {
            $requiredParms .= ' '.PANDOC_RTS;
        }
        else {
            // define stack size
            $requiredParms .= ' +RTS -K128m -RTS';
        }

        if (version_compare($pandocVersion, '1.14.0') >= 0) {
            $requiredParms .= ' --extract-media="'.rtrim(SERVER_FILES_PATH,'\\/').'" ';
        }

        $command = $requiredParms.' --data-dir="'.SERVER_FILES_PATH.'" -o "'.$outputPath.'" "'.$filePath.'"';
        Logger::getLogger('Commands')->info(get_class($this).': '.$command);

        try {
            \FileSystem::execPanDoc($command);
        }
        catch( \Exception $e ) {
            Logger::getLogger('Commands')->error(get_class($this).': '.$e->getMessage());
            throw $e;
        }

        $content = file_get_contents($outputPath);
        $content = preg_replace_callback( '/<img\s+([^>]*)>/i', array('HtmlImageConverter', 'replaceExternalImageCallback'), $content);
        $content = preg_replace_callback( '/<embed\s+([^>]*)>/i', array('HtmlImageConverter', 'replaceExternalImageCallback'), $content);
        $content = preg_replace('/<embed/i', '<img', $content);

        // append table borders
        $content = preg_replace('/<table>/i', '<table border="1">', $content);

        @unlink($outputPath);
        FileSystem::rmdirr(SERVER_FILES_PATH.'media');

        return \TextUtils::getUnstyledHtml($content);
    }

    protected function getVersion() {
        try {
            return \FileSystem::execPanDoc();
        }
        catch( \Exception $e ) {
            \Logger::getLogger('System')->error($e->getMessage());
            return '0';
        }
    }
}