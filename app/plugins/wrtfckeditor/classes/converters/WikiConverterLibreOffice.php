<?php
include_once SERVER_ROOT_PATH."core/classes/export/WikiIteratorExport.php";
include_once "WikiConverterPreviewExt.php";

abstract class WikiConverterLibreOffice extends WikiIteratorExport
{
    function __construct( $iterator )
    {
        parent::__construct($iterator);
        $this->htmlPath = \TextUtils::pathToUnixStyle(tempnam(SERVER_FILES_PATH, "lohtml"));
        $this->outputPath = SERVER_FILES_PATH . 'tmp/' . md5(uniqid('libreofficeoutput'));
        mkdir($this->outputPath, 0777, true);
        $this->outputPath = rtrim($this->outputPath, '\/\\') . '/';
    }

    function __destruct()
    {
        @unlink($this->htmlPath);
        FileSystem::rmdirr($this->outputPath);
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

        $command = ' --headless --infilter=writerglobal8_HTML --convert-to '.$this->getToParms().' --outdir "'.$this->outputPath.'" "'.$this->htmlPath.'"';
        Logger::getLogger('Commands')->info(get_class($this).': '.$command);

        try {
            \FileSystem::execLibreOffice($command);
        }
        catch( \Exception $e ) {
            Logger::getLogger('Commands')->error(get_class($this).': '.$e->getMessage());
            throw $e;
        }

        $info = pathinfo($this->htmlPath);
        $outputFilePath = $this->outputPath . $info['filename'] . $this->getExtension();
        if ( $templateId != '' ) {
            $this->postProcessByTemplate($templatePath, $outputFilePath);
        }

        $this->getIterator()->moveFirst();
        $documents = array_unique($this->getIterator()->fieldToArray('DocumentId'));
        $title = count($documents) < 2
            ? $this->getIterator()->getDisplayName()
            : $this->getName();

        header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
        header("Cache-control: no-store");
        header('Content-Type: '.$this->getMime());
        header(EnvironmentSettings::getDownloadHeader($title.$this->getExtension()));

        echo file_get_contents($outputFilePath);
	}

	protected function getDefaultTemplatePath() {
        return '';
    }

    protected function postProcessByTemplate( $templatePath, $documentPath ) {
    }

    protected function parseContent( &$content )
    {
        $content = \TextUtils::skipHtmlTag('tbody', $content);
        $content = \TextUtils::skipHtmlTag('thead', $content);
    }

    abstract protected function getToParms();
    abstract protected function getExtension();
    abstract protected function getMime();
}
