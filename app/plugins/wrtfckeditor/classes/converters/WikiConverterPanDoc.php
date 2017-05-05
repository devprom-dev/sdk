<?php
include_once SERVER_ROOT_PATH."core/classes/export/IteratorExport.php";
include "WikiConverterPreviewExt.php";

abstract class WikiConverterPanDoc extends IteratorExport
{
    function __construct( $iterator )
    {
        parent::IteratorExport($iterator);

        $this->htmlPath = str_replace("\\", "/", realpath(tempnam(SERVER_FILES_PATH, "pandochtml")));
        $this->outputPath = str_replace("\\", "/", realpath(tempnam(SERVER_FILES_PATH, "pandocoutput")));
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
        file_put_contents($this->htmlPath, ob_get_contents());
        ob_end_clean();

        $templatePath = $this->getDefaultTemplatePath();
        foreach($options as $option) {
            if ( strpos($option, 'template=') !== false ) {
                $templateId = array_pop(explode('=', $option));
                $templatePath = getFactory()->getObject('ExportTemplate')
                    ->getExact($templateId)->getFilePath('File');
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

        $requiredParms = '--from=html';
        if (version_compare($pandocVersion, '1.16.0') >= 0) {
            $requiredParms .= ' --dpi=196';
        }

        $command = 'pandoc '.$requiredParms.' '.$templateParm.' '.$this->getToParms().' --data-dir="'.SERVER_FILES_PATH.'" -o "'.$this->outputPath.'" "'.$this->htmlPath.'" 2>&1';
        Logger::getLogger('Commands')->info(get_class($this).': '.$command);

        $result = shell_exec($command);
        if ( $result != "" ) {
            Logger::getLogger('Commands')->error(get_class($this).': '.$result);
            throw new Exception($result);
        }

        if ( $templateParm != '' ) {
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

	protected function getVersion() {
        $command = 'pandoc -v 2>&1';
        Logger::getLogger('Commands')->info(get_class($this).': '.$command);
        return array_pop(
            preg_split('/\s+/',
                array_shift(
                    preg_split('/[\r\n]/', shell_exec($command))
                )
            )
        );
    }

	protected function getDefaultTemplatePath() {
        return '';
    }

    protected function postProcessByTemplate( $templatePath, $documentPath ) {
    }

    abstract protected function getToParms();
    abstract protected function getExtension();
    abstract protected function getMime();
    abstract protected function getTemplateParms( $filePath );
}
