<?php
include_once SERVER_ROOT_PATH . "pm/views/ui/converters/WikiConverterPreview.php";

class WikiConverterPreviewExt extends WikiConverterPreview
{
    function drawTitle()
    {
    }

    function configureParser($parser) {
        parent::configureParser($parser);

        // make image to be exported aligned to document width
        $parser->setImageRestoreCallback(
            function($data) {
                $matches = array();

                if ( preg_match('/src="([^"]+)"/i', $data, $matches) ) {
                    list($width, $height, $type, $attr) = getimagesize($matches[1]);
                    if ( $height > 0 ) {
                        $ratio = $width / $height;
                        $width = min(660, $width);
                        $height = round($width / $ratio, 0);
                        foreach( array('width' => $width, 'height' => $height) as $attribute => $value ) {
                            if ( preg_match("/{$attribute}=/i", $data, $matches) ) {
                                $data = preg_replace("/$attribute=\"?([^\"\s]+)\"?/i", "{$attribute}=\"{$value}\" ", $data);
                            }
                            else {
                                $data = preg_replace('/src="/i', "{$attribute}=\"{$value}\" src=\"", $data);
                            }
                        }
                    }
                }
                return $data;
            }
        );
    }
}