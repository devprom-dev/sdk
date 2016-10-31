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

        $templateParm = "";
        foreach($options as $option) {
            if ( strpos($option, 'template=') !== false ) {
                $templateId = array_pop(explode('=', $option));
                $templateParm = getFactory()->getObject('ExportTemplate')
                    ->getExact($templateId)->getFilePath('File');
                if ( !file_exists($templateParm) ) $templateParm = "";
            }
        }
        if ( $templateParm != "" ) {
            $templateParm = $this->getTemplateParms($templateParm);
        }

        $command = 'pandoc --dpi=196 --from=html '.$templateParm.' '.$this->getToParms().' --data-dir="'.SERVER_FILES_PATH.'" -o "'.$this->outputPath.'" "'.$this->htmlPath.'" 2>&1';
        Logger::getLogger('Commands')->info(get_class($this).': '.$command);

        $result = shell_exec($command);
        if ( $result != "" ) {
            Logger::getLogger('Commands')->error(get_class($this).': '.$result);
            throw new Exception($result);
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

    abstract protected function getToParms();
    abstract protected function getExtension();
    abstract protected function getMime();
    abstract protected function getTemplateParms( $filePath );
}
