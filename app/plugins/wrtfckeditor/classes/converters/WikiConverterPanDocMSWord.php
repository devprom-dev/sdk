<?php
include_once SERVER_ROOT_PATH . "core/classes/system/ZipSystem.php";
include_once "WikiConverterPanDoc.php";

class WikiConverterPanDocMSWord extends WikiConverterPanDoc
{
    function getToParms()
    {
        return "--to=docx";
    }

    function getExtension()
    {
        return ".docx";
    }

    function getMime()
    {
        return "application/msword";
    }

    function getTemplateParms( $filePath )
    {
        return '--reference-docx="'.$filePath.'"';
    }

    protected function getDefaultTemplatePath() {
        return SERVER_ROOT_PATH."templates/config/pandoc/reference.docx";
    }

    protected function postProcessByTemplate( $templatePath, $documentPath )
    {
        $templateExtractDir = SERVER_FILES_PATH . 'tmp/' . md5(uniqid('zip0'));
        mkdir($templateExtractDir, 0777, true);

        $docExtractDir = SERVER_FILES_PATH . 'tmp/' . md5(uniqid('zip1'));
        mkdir($docExtractDir, 0777, true);

        $result = ZipSystem::unzip($templatePath, $templateExtractDir);
        if ( $result != '' ) {
            \Logger::getLogger('System')->info($result);
        }

        ZipSystem::unzip($documentPath, $docExtractDir);
        if ( $result != '' ) {
            \Logger::getLogger('System')->info($result);
        }

        $documentContent = file_get_contents($docExtractDir . '/word/document.xml');

        preg_match('/<w:document\s*[^>]*>/i', $documentContent, $match);
        $documentTags = $match[0];

        $documentContent = array_pop(preg_split('/<w:body[^>]*>/i', $documentContent));
        $documentContent = array_shift(preg_split('/<w:sectPr[^>]*>/i', $documentContent));

        $templateContent = file_get_contents($templateExtractDir . '/word/document.xml');
        if ( strpos($templateContent, 'DEVPROM_DOCUMENT_BODY') !== false ) {
            $templateContent = preg_replace(
                '/DEVPROM_DOCUMENT_BODY\s*<\/w:t>\s*<\/w:r>/i',
                '</w:t></w:r></w:p>'.$documentContent.'<w:p><w:r><w:t></w:t></w:r>',
                $templateContent
            );
        }
        else {
            $templateContent = preg_replace(
                '/<\/w:body>/i',
                $documentContent.'</w:body>',
                $templateContent
            );
        }

        $templateContent = preg_replace(
            '/<w:document\s*[^>]*>/i', $documentTags, $templateContent
        );

        file_put_contents($docExtractDir . '/word/document.xml', $templateContent);

        ZipSystem::zipAll($documentPath, $docExtractDir);
        FileSystem::rmdirr($templateExtractDir);
        FileSystem::rmdirr($docExtractDir);
    }
}
