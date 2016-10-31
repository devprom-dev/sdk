<?php

class ImageDiff
{
    static function binary( $oldImage, $newImage )
    {
        if ( !class_exists('Imagick') ) return "";

        try {
            $old = new Imagick();
            if ( !$old->readImageBlob($oldImage) ) {
                throw new \Exception("Unable to read old image");
            }

            $new = new Imagick();
            if ( !$new->readImageBlob($newImage) ) {
                throw new \Exception("Unable to read new image");
            }

            $new->setOption('fuzz', '2%');
            $result = $new->compareImages( $old, Imagick::METRIC_ROOTMEANSQUAREDERROR );

            $resultImage = array_shift($result);
            $resultInfo = array_shift($result);

            if ( $resultInfo > 0.3 ) return "";

            $resultImage->setImageFormat('png');
            return $resultImage->getImageBlob();
        }
        catch( \Exception $e )
        {
            Logger::getLogger('System')->error($e->getMessage().PHP_EOL.$e->getTraceAsString());
            return "";
        }
    }
}