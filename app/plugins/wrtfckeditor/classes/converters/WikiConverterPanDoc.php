<?php
include_once SERVER_ROOT_PATH."core/classes/export/WikiIteratorExport.php";
include "WikiConverterPreviewExt.php";

abstract class WikiConverterPanDoc extends WikiIteratorExport
{
    private $pandocVersion = '';

    function __construct( $iterator )
    {
        parent::IteratorExport($iterator);

        $this->htmlPath = \TextUtils::pathToUnixStyle(tempnam(SERVER_FILES_PATH, "pandochtml"));
        $this->outputPath = \TextUtils::pathToUnixStyle(tempnam(SERVER_FILES_PATH, "pandocoutput"));
    }

    function __destruct()
    {
        @unlink($this->htmlPath);
        @unlink($this->outputPath);
    }

    function export()
	{
	    $options = $this->getOptions();

	    ob_start();
        $converter = new WikiConverterPreviewExt();
        $converter->setOptions($options);
        $converter->setObjectIt($this->getIterator());
        $converter->parse();

        $content = ob_get_contents();
        $this->parseContent($content);

        file_put_contents($this->htmlPath, $content);
        ob_end_clean();

        $templateId = '';
        $templatePath = $this->getDefaultTemplatePath();

        foreach($options as $option) {
            if ( strpos($option, 'template=') !== false ) {
                $templateId = array_pop(explode('=', $option));
                $templatePath = \TextUtils::pathToUnixStyle(
                    getFactory()->getObject('ExportTemplate')->getExact($templateId)->getFilePath('File')
                );
            }
        }
        if ( $templatePath != "" && file_exists($templatePath) ) {
            $templateParm = $this->getTemplateParms($templatePath);
        }
        else {
            $templateParm = "";
        }

        $pandocVersion = $this->getVersion();
        Logger::getLogger('Commands')->info('pandoc version is '.$pandocVersion);

        $requiredParms = '';
        if ( defined('PANDOC_RTS') && PANDOC_RTS != '' ) {
            $requiredParms .= ' '.PANDOC_RTS;
        }
        else {
            // define stack size
            $requiredParms .= ' +RTS -K512m -RTS';
        }

        $requiredParms .= ' --from=html';
        if (version_compare($pandocVersion, '1.16.0') >= 0) {
            $requiredParms .= ' --dpi=100';
        }

        $command = 'pandoc '.$requiredParms.' '.$templateParm.' '.$this->getToParms().' --data-dir="'.SERVER_FILES_PATH.'" -o "'.$this->outputPath.'" "'.$this->htmlPath.'" 2>&1';
        Logger::getLogger('Commands')->info(get_class($this).': '.$command);

        putenv("HOME=".trim(SERVER_FILES_PATH,"\\/"));
        $result = shell_exec($command);

        if ( $result != "" ) {
            Logger::getLogger('Commands')->error(get_class($this).': '.$result);
            if ( stripos($result, 'skipping.') === false && stripos($result, 'warning') === false ) {
                throw new Exception($result);
            }
        }

        if ( $templateParm != '' && $templateId != '' ) {
            $this->postProcessByTemplate($templatePath, $this->outputPath);
        }

        $this->getIterator()->moveFirst();
        $title = $this->getIterator()->count() == 1
            ? $this->getIterator()->getDisplayName()
            : $this->getName();

        header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
        header("Cache-control: no-store");
        header('Content-Type: '.$this->getMime());
        header(EnvironmentSettings::getDownloadHeader($title.$this->getExtension()));

        echo file_get_contents($this->outputPath);
	}

	protected function getVersion()
    {
        if ( $this->pandocVersion != '' ) return $this->pandocVersion;

        putenv("HOME=".trim(SERVER_FILES_PATH,"\\/"));
        $command = 'pandoc -v 2>&1';
        Logger::getLogger('Commands')->info(get_class($this).': '.$command);
        $result = shell_exec($command);
        Logger::getLogger('Commands')->info($result);
        return $this->pandocVersion = array_pop(
            preg_split('/\s+/',
                array_shift(
                    preg_split('/[\r\n]/', $result)
                )
            )
        );
    }

	protected function getDefaultTemplatePath() {
        return '';
    }

    protected function postProcessByTemplate( $templatePath, $documentPath ) {
    }

    protected function parseContent( &$content )
    {
        $colspanParts = preg_split('/colspan="/i', $content);
        foreach( $colspanParts as $key => $colspanContent ) {
            if ( $key == 0 ) continue;
            $matches = array();
            if ( preg_match('/^(\d+)/', $colspanContent, $matches) ) {
                if ( $matches[1] < 1 ) continue;
                $tdContent = preg_split('/<\/td>/i', $colspanContent);
                $colspanParts[$key] = join('</td>',
                    array_merge(
                        array(array_shift($tdContent)),
                        array_fill(0, $matches[1] - 1, '<td>'),
                        $tdContent
                    )
                );
            }
        }
        $content = join('dummy="', $colspanParts);
        $content = \TextUtils::removeHtmlTag('colgroup', $content);
    }

    abstract protected function getToParms();
    abstract protected function getExtension();
    abstract protected function getMime();
    abstract protected function getTemplateParms( $filePath );
}
