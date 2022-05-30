<?php
use Devprom\ProjectBundle\Service\Model\ModelService;

trait MSWordTemplateService
{
    function postProcessByTemplate( $templatePath, $documentPath, $options = array() )
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

        // make numberings unique before merge
        foreach( array('numbering.xml', 'styles.xml', 'document.xml') as $fileName ) {
            file_put_contents($docExtractDir . "/word/{$fileName}",
                $this->shiftNumberings(
                    file_get_contents($docExtractDir . "/word/{$fileName}"), 100
                )
            );
        }
        file_put_contents($docExtractDir . "/word/numbering.xml",
            $this->shiftNumberings(
                file_get_contents($docExtractDir . "/word/numbering.xml"), 100, 'w:abstractNum'
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

        // merge numberings into the template document
        file_put_contents($templateExtractDir . '/word/numbering.xml',
            $this->mergeNodes(
                file_get_contents($templateExtractDir . '/word/numbering.xml'),
                file_get_contents($docExtractDir . '/word/numbering.xml'),
                'w:abstractNum',
                '</w:abstractNum><w:num '
            )
        );

        file_put_contents($templateExtractDir . '/word/numbering.xml',
            $this->mergeNodes(
                file_get_contents($templateExtractDir . '/word/numbering.xml'),
                file_get_contents($docExtractDir . '/word/numbering.xml'),
                'w:num',
                '</w:numbering>'
            )
        );

        if ( $options['bullet'] != '' ) {
            file_put_contents($templateExtractDir . '/word/numbering.xml',
                $this->replaceNumberings(
                    file_get_contents($templateExtractDir . '/word/numbering.xml'),
                    'bullet', $options['bullet']
                )
            );
        }

        if ( $options['numbered'] != '' ) {
            file_put_contents($templateExtractDir . '/word/numbering.xml',
                $this->replaceNumberings(
                    file_get_contents($templateExtractDir . '/word/numbering.xml'),
                    'decimal', $options['numbered']
                )
            );
        }

        $overiddenHeadings = array();
        $templateStyleContent = file_get_contents($templateExtractDir . '/word/styles.xml');
        $documentContent = $this->updateHeadings( $templateStyleContent, $documentContent, $overiddenHeadings );

        $contentStyles = array();
        $templateStyles = array();
        $documentStyleContent = file_get_contents($docExtractDir . '/word/styles.xml');

        preg_match_all('/w:styleId="([^"]+)"/i', $templateStyleContent, $templateStyles);
        preg_match_all('/w:styleId="([^"]+)"/i', $documentStyleContent, $contentStyles);
        $missedStyles = array_diff(
            array_unique($contentStyles[1]),
            array_unique($templateStyles[1]),
            array(
                'Heading',
                'Normal',
                'List',
                'Caption',
                'Index'
            ),
            $overiddenHeadings
        );

        if ( count($missedStyles) > 0 ) {
            file_put_contents($templateExtractDir . '/word/styles.xml',
                $this->mergeStyles(
                    $templateStyleContent, $documentStyleContent, $missedStyles
                )
            );
        }

        file_put_contents($templateExtractDir . '/word/document.xml',
            $this->mergeContent( $documentContent,
                preg_replace_callback_array(
                    array (
                        REGEX_FIELD_SUBSTITUTION => array($this, 'parseFieldSubstitution')
                    ),
                    file_get_contents($templateExtractDir . '/word/document.xml')
                )
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

    function updateHeadings( $stylesContent, $documentContent, &$overridenHeadings )
    {
        $headingStyles = array(
            'Heading1' => 'Heading1',
            'Heading2' => 'Heading2',
            'Heading3' => 'Heading3',
            'Heading4' => 'Heading4',
            'Heading5' => 'Heading5',
            'Heading6' => 'Heading6',
            'Heading7' => 'Heading7',
            'Heading8' => 'Heading8',
            'Heading9' => 'Heading9'
        );

        // get specific heading styles and map them to default ones
        $styles = explode('<w:style ', $stylesContent);
        $matches = array();

        foreach( $styles as $styleBody ) {
            preg_match('/w:styleId="([^"]+)"/i', $styleBody, $matches);
            $styleId = $matches[1];

            if ( stripos($styleBody, 'w:type="paragraph"') === false ) continue;
            $outlineAttributes = explode('<w:outlineLvl ', $styleBody);
            if ( count($outlineAttributes) < 2 ) continue;

            preg_match('/w:val="(\d+)"/i', $outlineAttributes[1], $matches);
            $outlineLevel = $matches[1];
            $defaultStyleId = 'Heading' . ($outlineLevel + 1);
            if ( $headingStyles[$defaultStyleId] != $defaultStyleId ) continue;

            $headingStyles[$defaultStyleId] = $styleId;
            $overridenHeadings[] = $defaultStyleId;
        }

        foreach( $headingStyles as $defaultStyleId => $specificStyleId ) {
            if ( $defaultStyleId == $specificStyleId ) continue;
            $documentContent = str_replace(
                "w:val=\"{$defaultStyleId}\"",
                "w:val=\"{$specificStyleId}\"",
                $documentContent
            );
        }

        return $documentContent;
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

    function mergeStyles( $targetStyle, $sourceStyle, $missedStyles )
    {
        $stylesString = '';
        foreach( explode('<w:style ', $sourceStyle) as $styleText ) {
            foreach( $missedStyles as $styleId ) {
                if ( strpos($styleText, "w:styleId=\"{$styleId}\"") !== false ) {
                    $textParts = explode("</w:style>", $styleText);
                    $stylesString .= '<w:style ' . $textParts[0] . "</w:style>";
                }
            }
        }

        return str_replace('</w:styles>', $stylesString . '</w:styles>', $targetStyle);
    }

    function mergeNodes( $targetContent, $sourceContent, $tag, $tail )
    {
        $nodesString = '';
        $nodes = explode("<{$tag} ", $sourceContent);
        array_shift($nodes);

        foreach( $nodes as $nodeText ) {
            // suppress styles definition inside numberings
            $textParts = explode("</{$tag}>", $nodeText);
            $nodesString .= "<{$tag} " . $textParts[0] . "</{$tag}>";
        }

        if ( strpos($tail, "</{$tag}>") !== false ) {
            $nodesString = "</{$tag}>" . $nodesString . str_replace("</{$tag}>", '', $tail);
        }
        else {
            $nodesString .= $tail;
        }

        return $nodesString == ''
            ? $targetContent
            : str_replace($tail, $nodesString, $targetContent);
    }

    function shiftNumberings( $targetContent, $shift, $tag = 'w:num' )
    {
        $targetContent = preg_replace_callback(
            "/<{$tag} {$tag}Id=\"(\d)+\"/",
            function( $match ) use ($shift, $tag) {
                return "<{$tag} {$tag}Id=\"" . (intval($match[1]) + $shift) . "\"";
            },
            $targetContent);

        $targetContent = preg_replace_callback(
            "/<{$tag}Id w:val=\"(\d)+\"/",
            function( $match ) use ($shift, $tag) {
                return "<{$tag}Id w:val=\"" . (intval($match[1]) + $shift) . "\"";
            },
            $targetContent);

        return $targetContent;
    }

    function replaceNumberings( $content, $formatType, $template )
    {
        $lines = explode('<w:abstractNum', $content);
        foreach( $lines as $key => $line ) {
            $matches = array();
            if ( preg_match('/<w:numFmt\s+w:val=\"([^\"]+)\"/', $line, $matches) !== false ) {
                if ( $matches[1] == $formatType ) {
                    preg_match('/w:abstractNumId=\"([^\"]+)\"/', $line, $matches);
                    $lines[$key] = " w:abstractNumId=\"{$matches[1]}\">" . $template . "</w:abstractNum>";
                }
            }
        }
        return join('<w:abstractNum', $lines);
    }

    function parseFieldSubstitution( $match )
    {
        $this->getIterator()->moveFirst();
        $result = ModelService::computeFormula(
            $this->getIterator(), '{' . $match[1] . '}'
        );
        $lines = array();
        foreach ($result as $computedItem) {
            if (!is_object($computedItem)) {
                $lines[] = TextUtils::stripAnyTags($computedItem);
            } else {
                $lines[] = $computedItem->getDisplayName();
            }
        }
        return join(', ', $lines);
    }
}