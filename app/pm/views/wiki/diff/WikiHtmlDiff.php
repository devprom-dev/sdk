<?php
use Caxy\HtmlDiff\HtmlDiff;
include "ImageDiff.php";

class WikiHtmlDiff extends HtmlDiff
{
    const NEW_INDEX_OFFSET = 10000;
    private $oldImages = array();
    private $newImages = array();

    public function __construct($oldText, $newText, $encoding = 'UTF-8', $specialCaseTags = null, $groupDiffs = null)
    {
//        $oldText = preg_replace_callback('/<img\s+([^>]+)>/i', array($this, 'extractOldImage'), $oldText);
//        $newText = preg_replace_callback('/<img\s+([^>]+)>/i', array($this, 'extractNewImage'), $newText);

        parent::__construct($oldText, $newText, $encoding, $specialCaseTags, $groupDiffs);
    }

    protected function diffElementsByAttribute($oldText, $newText, $attribute, $element)
    {
        switch( $element ) {
            case 'img':
                switch( $attribute ) {
                    case 'src':
                        $oldAttribute = $this->getAttributeFromTag($oldText, $attribute);
                        $newAttribute = $this->getAttributeFromTag($newText, $attribute);

                        if ( is_numeric($oldAttribute) ) {
                            $oldAttribute = $this->oldImages[intval($oldAttribute)];
                        }
                        if ( is_numeric($newAttribute) ) {
                            $newAttribute = $this->newImages[intval($newAttribute) - self::NEW_INDEX_OFFSET];
                        }

                        if ($oldAttribute !== $newAttribute) {
                            $result = $this->diffImages(
                                HtmlImageConverter::decodeBase64Image($oldAttribute),
                                HtmlImageConverter::decodeBase64Image($newAttribute)
                            );
                            if ( $result != "" ) return $result;
                        }
                        else {
                            if ( is_numeric($oldAttribute) ) {
                                return $this->oldImages[$oldAttribute];
                            }
                        }
                }
        }
        return parent::diffElementsByAttribute($oldText, $newText, $attribute, $element);
    }

    protected function diffImages( $oldImage, $newImage )
    {
        $resultImage = ImageDiff::binary($oldImage, $newImage);
        if ( $resultImage == "" ) return $resultImage;
        return HtmlImageConverter::encodeBase64Image($resultImage);
    }

    public function build()
    {
        try {
            return parent::build();
        }
        catch(\Exception $e) {
            \Logger::getLogger('System')->error($e->getMessage().$e->getTraceAsString());
            return "";
        }
    }

    protected function purifyHtml($html) {
        return $html;
    }

    function extractImage( $match, &$array, $index = 0 ) {
        $matches = array();
        if ( preg_match('/src="([^"]+)"/i', $match[1], $matches) ) {
            return '<img src="' . ($index + array_push($array, $matches[1])) . '">';
        }
        else {
            return $match[0];
        }
    }

    function extractOldImage( $match ) {
        return $this->extractImage($match, $this->oldImages);
    }

    function extractNewImage( $match ) {
        return $this->extractImage($match, $this->newImages, self::NEW_INDEX_OFFSET);
    }
}