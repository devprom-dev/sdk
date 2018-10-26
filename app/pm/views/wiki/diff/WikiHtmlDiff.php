<?php
use Caxy\HtmlDiff\HtmlDiff;
include "ImageDiff.php";

class WikiHtmlDiff extends HtmlDiff
{
    protected function diffElementsByAttribute($oldText, $newText, $attribute, $element)
    {
        switch( $element ) {
            case 'img':
                switch( $attribute ) {
                    case 'src':
                        $oldAttribute = $this->getAttributeFromTag($oldText, $attribute);
                        $newAttribute = $this->getAttributeFromTag($newText, $attribute);

                        if ($oldAttribute !== $newAttribute) {
                            $result = $this->diffImages(
                                HtmlImageConverter::decodeBase64Image($oldAttribute),
                                HtmlImageConverter::decodeBase64Image($newAttribute)
                            );
                            if ( $result != "" ) return $result;
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
}