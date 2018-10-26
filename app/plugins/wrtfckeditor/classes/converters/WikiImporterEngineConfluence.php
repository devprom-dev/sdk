<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE
include_once SERVER_ROOT_PATH . "pm/classes/wiki/converters/WikiImporterEngine.php";

class WikiImporterEngineConfluence extends WikiImporterEngine
{
    private $filesPath = '';

    protected function getHtml( $filePath )
    {
        try {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            if ( strpos($finfo->file($filePath), 'zip') === false ) return "";

            $extractDir = SERVER_FILES_PATH . 'tmp/' . md5(uniqid('confluence'));
            mkdir($extractDir, 0777, true);

            $result = ZipSystem::unzip($filePath, $extractDir);
            if ( $result != '' ) {
                \Logger::getLogger('System')->info($result);
            }

            // get space path
            foreach(glob($extractDir . '/*') as $file) {
                if(is_dir($file)) {
                    $extractDir = $file;
                    break;
                }
            }
            if ( !file_exists($extractDir . '/index.html') ) return "";

            $this->filesPath = trim($extractDir, '\\/').'/';
            $content = $this->parseIndex($extractDir);

            FileSystem::rmdirr($extractDir);
            return $content;
        }
        catch( Exception $e ) {
            \Logger::getLogger('System')->error($e->getMessage().$e->getTraceAsString());
        }
        return "";
    }

    protected function parseIndex( $extractDir )
    {
        $parts = preg_split('/<ul>/i', file_get_contents($extractDir . '/index.html'));
        array_shift($parts);
        $text = '<ul>' . join('<ul>', $parts);
        $parts = preg_split('/<\/ul>/i', $text);
        array_pop($parts);
        $text = join('</ul>', $parts) . '</ul>';

        return $this->ul_to_array($text, $extractDir, 0);
    }

    protected function parseHtmlFile( $filePath )
    {
        $content = '';
        $text = '<?xml version="1.0" encoding="'.APP_ENCODING.'"?>'.file_get_contents($filePath);

        $was_state = libxml_use_internal_errors(true);
        $doc = new \DOMDocument("1.0", APP_ENCODING);
        if ( $doc->loadHTML($text) ) {
            $bodyElement = $doc->getElementById('main-content');
            if ( $bodyElement ) {
                $content .= preg_replace_callback(
                    '/\s+src="([^d][^"]+)"/i',
                    array($this, 'parseAttachmentCallback'),
                    $doc->saveHTML($bodyElement)
                );
                $content = preg_replace('/<h([0-9])[^>]*>/', '<hh\\1>', $content);
                $content = preg_replace('/<\/h([0-9])>/', '</hh\\1>', $content);
            }
        }
        libxml_clear_errors();
        libxml_use_internal_errors($was_state);
        return $content;
    }

    protected function parseAttachmentCallback( $match )
    {
        if ( preg_match('/attachments\//', $match[1], $result) ) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $filePath = $this->filesPath . $match[1];
            return ' src="data:'.$finfo->file($filePath).';base64,'.\TextUtils::encodeImage($filePath).'"';
        }
    }

    protected function ul_to_array($ul, $extractDir, $level)
    {
        if (is_string($ul)) {
            if (!$ul = simplexml_load_string($ul)) {
                return FALSE;
            }
            return $this->ul_to_array($ul, $extractDir, $level + 1);
        } else if (is_object($ul)) {
            $content = '';
            foreach ($ul->li as $li) {
                if ( isset($li->a) ) {
                    $content .= '<h'.$level.'>' . $li->a . '</h'.$level.'>';
                    $attribute = 'href';
                    $content .= $this->parseHtmlFile($extractDir . '/' . $li->a->attributes()->$attribute);
                }
                foreach( $li->ul as $nested_ul ) {
                    $content .= $this->ul_to_array($nested_ul, $extractDir, min(6, $level + 1));
                }
            }
            return $content;
        } else return '';
    }
}