<?php

class HtmlImageConverter
{
    static function replaceImageCallback( $match )
    {
        $attributes = $match[1];

        if ( preg_match( '/src="([^"]+)"/i', $attributes, $attrs ) ) $url = $attrs[1];
        if ( $url == '' ) return $match[0];
        if ( !preg_match('/\/file\/([a-zA-Z_]+)\/([^\/]+)\/([\d]+).*/i', $url, $result) ) return $match[0];

        $file_class = $result[1];
        $file_project = $result[2];
        $file_id = $result[3];

        $file_class = getFactory()->getClass($file_class);
        if ( class_exists($file_class) ) {
            $object = getFactory()->getObject($file_class);
        } else {
            $object = new Metaobject($file_class);
        }

        $file_it = $object->getRegistry()->Query(
            array(
                new FilterInPredicate($file_id)
            )
        );
        if ( $file_it->getId() < 1 ) return $match[0];

        $file_attribute = '';
        foreach( $object->getAttributes() as $attribute => $data ) {
            if ( in_array($object->getAttributeType($attribute), array('file','image')) ) {
                $file_attribute = $attribute;
                break;
            }
        }
        if ( $file_attribute == '' ) return $match[0];

        $path = $file_it->getFilePath($file_attribute);
        $image = file_get_contents($path);
        if ( $image === false ) return $match[0];

        if ( strpos($image, '</svg>') > 0 ) {
            $mime = 'image/svg+xml';
        }
        else {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($path);
        }

        $src = 'data:'.$mime.';base64,'.\TextUtils::encodeImage($path);
        $match[0] = preg_replace('/src="[^"]+"/i', 'src="'.$src.'"', $match[0]);

        return $match[0];
    }

    static function replaceExternalImageCallback( $match )
    {
        $attributes = $match[1];

        if ( preg_match( '/src="([^"]+)"/i', $attributes, $attrs ) ) $url = $attrs[1];
        if ( $url == '' ) return $match[0];
        if ( strpos($url, 'base64') !== false ) return $match[0];

        $image = file_get_contents($url);
        if ( $image === false ) return $match[0];

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($image);

        $src = 'data:'.$mime.';base64,'.\TextUtils::encodeImage($url);
        $match[0] = preg_replace('/src="[^"]+"/i', 'src="'.$src.'"', $match[0]);

        return $match[0];
    }

    static function decodeBase64Image( $data )
    {
        $matches = array();
        preg_match('/data:image([^;]*);base64,([^"\s]+)/', $data, $matches);
        return base64_decode($matches[2]);
    }

    static function encodeBase64Image( $data ) {
        return '<img src="data:image/png;base64,'.base64_encode($data).'">';
    }
}