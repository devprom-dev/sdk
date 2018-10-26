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

        $command = 'pandoc '.$requiredParms.' --data-dir="'.SERVER_FILES_PATH.'" -o "'.$outputPath.'" "'.$filePath.'" 2>&1';
        Logger::getLogger('Commands')->info(get_class($this).': '.$command);

        putenv("HOME=".trim(SERVER_FILES_PATH,"\\/"));
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
        $content = preg_replace_callback( '/<embed\s+([^>]*)>/i', array('HtmlImageConverter', 'replaceExternalImageCallback'), $content);
        $content = preg_replace('/<embed/i', '<img', $content);

        // append table borders
        $content = preg_replace('/<table>/i', '<table border="1">', $content);

        @unlink($outputPath);
        FileSystem::rmdirr(SERVER_FILES_PATH.'media');

        return $content;
    }

    protected function getVersion() {
        putenv("HOME=".trim(SERVER_FILES_PATH,"\\/"));
        $command = 'pandoc -v 2>&1';
        Logger::getLogger('Commands')->info(get_class($this).': '.$command);
        $result = shell_exec($command);
        Logger::getLogger('Commands')->info($result);
        return array_pop(
            preg_split('/\s+/',
                array_shift(
                    preg_split('/[\r\n]+/', $result)
                )
            )
        );
    }
}