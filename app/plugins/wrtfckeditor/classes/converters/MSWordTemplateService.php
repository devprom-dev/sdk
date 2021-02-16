<?php

trait MSWordTemplateService
{
    function postProcessByTemplate( $templatePath, $documentPath )
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

        $ids = array();

        file_put_contents($templateExtractDir . '/word/_rels/document.xml.rels',
            $this->mergeRelationships(
                file_get_contents($docExtractDir . '/word/_rels/document.xml.rels'),
                file_get_contents($templateExtractDir . '/word/_rels/document.xml.rels'),
                $ids
            )
        );

        $documentContent = file_get_contents($docExtractDir . '/word/document.xml');
        $documentContent = preg_replace_callback(
            '/r:(id|embed)="rId([\d]+)"/i',
            function($match) use ($ids) {
                if ( !in_array($match[2], $ids) ) return $match[0];
                return 'r:' . $match[1] . '="rId' . (10000 + intval($match[2])) . '"';
            },
            $documentContent
        );

        $documentContent = preg_replace_callback(
            '/w:id="([\d]+)"/i',
            function($match) {
                return 'w:id="' . (10000 + intval($match[1])) . '"';
            },
            $documentContent
        );

        file_put_contents($templateExtractDir . '/word/document.xml',
            $this->mergeContent( $documentContent,
                file_get_contents($templateExtractDir . '/word/document.xml')
            )
        );

        mkdir($templateExtractDir . "/word/media");
        $contentTypes = array();

        foreach (glob($docExtractDir . "/word/media/*") as $file) {
            if( is_dir($file) ) continue;
            $pathInfo = pathinfo($file);
            if ( $pathInfo['extension'] != '' ) {
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $contentTypes[$pathInfo['extension']] = $finfo->file($file);
                $dest = realpath($templateExtractDir . "/word/media") . '/' . basename($file);
                copy($file, $dest);
            }
        }

        $contentType = file_get_contents($templateExtractDir . '/[Content_Types].xml');
        $contentTypeString = "";
        foreach( $contentTypes as $ext => $mime ) {
            if ( strpos($contentType, '="'.$ext.'"') !== false ) continue;
            $contentTypeString .= '<Default Extension="'.$ext.'" ContentType="'.$mime.'"/>';
        }

        file_put_contents($templateExtractDir . '/[Content_Types].xml',
            str_replace( '</Types>', $contentTypeString.'</Types>', $contentType)
        );

        unlink($documentPath);
        ZipSystem::zipAll($documentPath, $templateExtractDir);

        FileSystem::rmdirr($templateExtractDir);
        FileSystem::rmdirr($docExtractDir);
    }

    function mergeContent( $documentContent, $templateContent )
    {
        list($documentHeader, $documentBody) = preg_split('/<w:body[^>]*>/i', $documentContent);
        list($documentBody, $documentFooter) = preg_split('/<w:sectPr\s*/i', $documentBody);

        $templateContent = preg_replace('/w14:paraId="[^"]+"/i', '', $templateContent);
        $templateContent = preg_replace('/w14:textId="[^"]+"/i', '', $templateContent);
        list($templateHeader, $templateBody) = preg_split('/<w:body[^>]*>/i', $templateContent);

        if ( strpos($templateBody, 'DEVPROM_DOCUMENT_BODY') !== false ) {
            $templateBody = preg_replace('/<w:bookmark[^>]+>/', '', $templateBody);
            $templateBody = preg_replace(
                '/DEVPROM_DOCUMENT_BODY\s*<\/w:t>\s*<\/w:r><\/w:p>/i',
                '</w:t></w:r></w:p>'.$documentBody,
                $templateBody
            );
        }
        else {
            $parts = preg_split('/<w:sectPr\s*/i', $templateBody);
            $parts[count($parts) - 2] .= $documentBody;
            $templateBody = join('<w:sectPr ' , $parts);
        }

        if ( strpos($templateHeader, 'xmlns:a=') === false ) {
            $templateHeader = preg_replace('/<w:document\s+/i', '<w:document xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" ', $templateHeader);
        }
        if ( strpos($templateHeader, 'xmlns:pic=') === false ) {
            $templateHeader = preg_replace('/<w:document\s+/i', '<w:document xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture" ', $templateHeader);
        }

        $result = $templateHeader . '<w:body>' . $templateBody;
        return $result;
    }

    function mergeRelationships( $documentRels, $templateRels, &$ids )
    {
        $templateRelsParts = preg_split('/<Relationship\s+/i', $templateRels);
        $documentRelsParts = array_filter(
            preg_split('/<Relationship\s+/i', str_replace('</Relationships>', '', $documentRels)),
            function( $item ) {
                return strpos($item, 'media/') > 0 || strpos($item, '/hyperlink') > 0;
            }
        );

        foreach( $documentRelsParts as $key => $row ) {
            $documentRelsParts[$key] = preg_replace_callback( '/Id="rId([\d]+)"/i',
                function($match) use (&$ids) {
                    $ids[] = $match[1];
                    return 'Id="rId' . (10000 + intval($match[1])) . '"';
                },
                $documentRelsParts[$key]
            );
        }

        $header = array_shift($templateRelsParts);
        $templateRelsParts = array_merge($documentRelsParts, $templateRelsParts);
        array_unshift($templateRelsParts, $header);

        return join('<Relationship ', $templateRelsParts);
    }
}