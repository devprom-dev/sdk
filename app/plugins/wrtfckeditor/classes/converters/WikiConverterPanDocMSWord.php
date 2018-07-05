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

        $templateContent = file_get_contents($templateExtractDir . '/word/document.xml');
        $templateContent = preg_replace('/w14:paraId="[^"]+"/i', '', $templateContent);
        $templateContent = preg_replace('/w14:textId="[^"]+"/i', '', $templateContent);

        if ( strpos($templateContent, 'DEVPROM_DOCUMENT_BODY') !== false )
        {
            list($documentHeader, $documentBody) = preg_split('/<w:body[^>]*>/i', $documentContent);
            list($documentBody, $documentFooter) = preg_split('/<w:sectPr/i', $documentBody);

            $templateContent = array_pop(preg_split('/<w:body[^>]*>/i', $templateContent));
            $templateContent = array_shift(preg_split('/<w:sectPr[^>]*>/i', $templateContent));

            $templateContent = preg_replace(
                '/DEVPROM_DOCUMENT_BODY\s*<\/w:t>\s*<\/w:r>/i',
                '</w:t></w:r></w:p>'.$documentBody.'<w:p><w:r><w:t></w:t></w:r>',
                $templateContent
            );

            $documentContent = $documentHeader . '<w:body>' . $templateContent . '<w:sectPr' . $documentFooter;
        }
        else {
            $templateContent = array_pop(preg_split('/<w:body[^>]*>/i', $templateContent));
            $templateContent = array_shift(preg_split('/<w:sectPr[^>]*>/i', $templateContent));
            $documentContent = preg_replace(
                '/<w:body>/i',
                '<w:body>'.$templateContent,
                $documentContent
            );
        }

        file_put_contents($docExtractDir . '/word/document.xml', $documentContent);

        ZipSystem::zipAll($documentPath, $docExtractDir);
        FileSystem::rmdirr($templateExtractDir);
        FileSystem::rmdirr($docExtractDir);
    }
}
