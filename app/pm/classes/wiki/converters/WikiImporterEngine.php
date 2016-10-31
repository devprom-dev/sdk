<?php

abstract class WikiImporterEngine
{
    abstract protected function getHtml( $filePath );

    function __construct()
    {
        $this->filePath = str_replace("\\", "/", realpath(tempnam(SERVER_FILES_PATH, "pandocoutput")));
    }

    function __destruct() {
        unlink($this->filePath);
    }

    public function import( WikiPage $object, $documentTitle, $rawData )
    {
        $info = pathinfo($documentTitle);
        $documentTitle = $info['filename'];

        $this->filePath = $this->filePath.'.'.$info['extension'];
        file_put_contents($this->filePath, $rawData);

        $html = $this->getHtml($this->filePath);
        if ( $html == '' ) return false;

        $sections = preg_split('/<h[1-6][^>]*>/i', $html);
        $documentContent = array_shift($sections);

        $this->document_it = $object->getExact($object->add_parms(
            array (
                'Caption' => $documentTitle,
                'Content' => $documentContent
            )
        ));

        $levels = array ();
        foreach( range(0, 6) as $level ) {
            $levels[$level] = $this->document_it->getId();
        }

        foreach( $sections as $section ) {
            preg_match('/<\/h([1-6])>/i', $section, $matches);
            $selfLevel = $matches[1];

            list($title, $content) = preg_split('/<\/h[1-6]>/i', $section);

            $totext = new \Html2Text\Html2Text(trim($title), array('width'=>0));
            $title = preg_replace('/[\r\n]+/', ' ', $totext->getText());

            $content = trim(trim($content), PHP_EOL);
            if ( $title == '' && $content == '' ) continue;

            $page_it = $object->getExact($object->add_parms(
                array (
                    'Caption' => $title,
                    'Content' => $content,
                    'ParentPage' => $levels[$selfLevel - 1]
                )
            ));
            $levels[$selfLevel] = $page_it->getId();
        }

        return true;
    }

    function getDocumentIt() {
        return $this->document_it;
    }

    private $document_it = null;
}