<?php
include_once SERVER_ROOT_PATH . "pm/classes/wiki/converters/WikiImporterEngine.php";

class WikiImporterEnginePdf extends WikiImporterEngine
{
    protected function getHtml( $filePath )
    {
        $content = '';

        try {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            if ( strpos($finfo->file($filePath), 'pdf') === false ) return $content;

            if ( !class_exists('Imagick') ) throw new Exception('Imagick module is not installed');

            // detects type of the file
            $image = new Imagick();
            if ( !$image->pingImage($filePath) ) {
                \Logger::getLogger('System')->info("WikiImporterEnginePdf: cant ping image ".$filePath);
                return $content;
            }
            $pagesNumber = $image->getNumberImages();
            if ( $pagesNumber < 1 ) return $content;

            $image = new Imagick($filePath);
            $image->setImageBackgroundColor('white');
            //$image->setResolution( 1200, 1200 );

            for( $i = 0; $i < $pagesNumber; $i++ )
            {
                $image->setIteratorIndex($i);
                $image->setImageFormat( "png" );

                $outputPath = tempnam(sys_get_temp_dir(), "importer_engine");
                $image->writeImage($outputPath);
                $imageData = \TextUtils::encodeImage($outputPath);
                unlink($outputPath);

                if ( $imageData != "" ) {
                    $content .= '<h1>'.translate('Страница').' '.($i + 1).'</h1><img src="data:image;base64,'.$imageData.'">';
                }
            }
        }
        catch( Exception $e ) {
            \Logger::getLogger('System')->error($e->getMessage().$e->getTraceAsString());
            throw $e;
        }

        return $content;
    }
}