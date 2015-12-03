<?php

class HtmlImageConverter
{
    static function replaceImageCallback( $match )
    {
        $attributes = $match[1];

        if ( preg_match( '/src="([^"]+)"/i', $attributes, $attrs ) ) $url = $attrs[1];
        if ( $url == '' ) return $match[0];
        if ( !preg_match('/file\/([^\/]+)\/([^\/]+)\/([\d]+).*/i', $url, $result) ) return $match[0];

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

        $image = file_get_contents($file_it->getFilePath($file_attribute));
        if ( $image === false ) return $match[0];

        $src = 'data:image;base64,'.base64_encode($image);
        $match[0] = preg_replace('/src="[^"]+"/i', 'src="'.$src.'"', $match[0]);

        return $match[0];
    }
}