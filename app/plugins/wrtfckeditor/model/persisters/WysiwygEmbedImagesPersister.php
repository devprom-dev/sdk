<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

class WysiwygEmbedImagesPersister extends ObjectSQLPersister
{
    private $fields = null;
    private $file = null;

    function map( & $parms )
	{
        $this->file = getFactory()->getObject('cms_TempFile');
        foreach( $this->getFields() as $field ) {
            if ( $parms[$field] == '' ) continue;
            $parms[$field] = preg_replace_callback('/\s+src="([^"]*)"/i', array($this, 'embedImages'), $parms[$field]);
        }
    }

    protected function getFields()
    {
        if ( is_array($this->fields) ) return $this->fields;
        foreach( $this->getObject()->getAttributes() as $attribute => $data ) {
            if ( $this->getObject()->getAttributeType($attribute) != 'wysiwyg' ) continue;
            $this->fields[] = $attribute;
        }
        return $this->fields;
    }

    function embedImages( $match )
    {
        $url = $match[1];

        $found = array();
        if ( !preg_match('/file\/([^\/]+)\/([^\/]+)\/([\d]+).*/', $url, $found) ) {
            if ( !preg_match('/file\/([^\/]+)\/([\d]+).*/', $url, $found) ) return $match[0];
            $file_class = $found[1];
            $file_id = $found[2];
        } else {
            $file_class = $found[1];
            $file_id = $found[3];
        }
        if ( $file_class != 'cms_TempFile' ) return $match[0];

        $file_it = $this->file->getExact($file_id);
        if ( $file_it->getId() == '' ) return $match[0];

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $path = $file_it->getFilePath('File');
        return ' src="data:'.$finfo->file($path).';base64,'.base64_encode(file_get_contents($path)).'"';
    }
}

