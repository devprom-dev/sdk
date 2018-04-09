<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE
define( 'REGEX_COMMENTS', '/<span\s+comment-id="(\d+)"\s*>([^<]+)<\/span>/i' );
define( 'TABLE_ROW_NUMBERING', '/<t(d|h)>\s*<ol\s*([^>]*)>/i' );
define( 'REGEX_HREF_UID', '/<a\s*[^>]*>\s*(http|https):\/\/[^\/]+\/pm\/[^\/]+\/([A-Z]{1}-[0-9]+)\s*<\/a>/i' );

class FieldWysiwygPersister extends ObjectSQLPersister
{
    private $fields = null;
    private $codeBlocks = array();
    private $tableRowIndex = 0;
    private $comment_it = null;

    function map( & $parms )
	{
        foreach( $this->getFields() as $field ) {
            if ( $parms[$field] == '' ) continue;

            $idValue = $parms[$this->getObject()->getIdAttribute()];
            if ( $idValue != '' ) {
                $objectIt = $this->getObject()->getExact($idValue);
                $this->comment_it = getFactory()->getObject('Comment')->getAllForObject($objectIt);
            }

            $parms[$field] = TextUtils::getValidHtml(
                $this->parseField($parms[$field])
            );

            $this->codeBlocks = array();
            $this->tableRowIndex = 0;
            unset($this->comment_it);
        }
    }

    function parseField( $content )
    {
        $callbacks = array(
            CODE_ISOLATE => array($this, 'codeIsolate'),
            '/<img\s+([^>]*)>/i' => array('HtmlImageConverter', 'replaceImageCallback'),
            REGEX_HREF_UID => array($this, 'replaceHrefWithUid'),
            REGEX_COMMENTS => array($this, 'checkComments'),
            TABLE_ROW_NUMBERING => array($this, 'tableRowNumbering'),
            '/<div><\/div>/i' => function($match) {
                return "";
            },
            CODE_RESTORE => array($this, 'codeRestore')
        );

        if ( function_exists('preg_replace_callback_array') ) {
            return preg_replace_callback_array($callbacks, $content);
        }
        else {
            foreach( $callbacks as $regexp => $callback ) {
                $content = preg_replace_callback($regexp, $callback, $content);
            }
            return $content;
        }
    }

    protected function getFields()
    {
        if ( is_array($this->fields) ) return $this->fields;
        return $this->fields = $this->getObject()->getAttributesByType('wysiwyg');
    }

    function codeIsolate( $match ) {
        return '<code'.$match[1].'>'.array_push($this->codeBlocks, $match[2]).'</code>';
    }

    function codeRestore( $match ) {
        return '<code'.$match[1].'>'.$this->codeBlocks[$match[2] - 1].'</code>';
    }

    function tableRowNumbering( $match )
    {
        if ( strpos($match[2], 'start') === false ) {
            return '<t'.$match[1].'><ol start="'.(++$this->tableRowIndex).'" '.$match[2].'>';
        }
        else {
            if ( preg_match('/start="(\d+)"/i', $match[2], $result) ) {
                $this->tableRowIndex = $result[1];
            }
            return $match[0];
        }
    }

    function checkComments( $match )
    {
        if ( !is_object($this->comment_it) ) return $match[0];

        $this->comment_it->moveToId($match[1]);
        if ( $this->comment_it->getId() != $match[1] ) {
            return $match[2];
        }
        else {
            return $match[0];
        }
    }

    function replaceHrefWithUid( $match )
    {
        $uid = new ObjectUid();
        $objectIt = $uid->getObjectIt($match[2]);
        if ( $objectIt->getId() != '' ) {
            return $match[2];
        }
        else {
            return $match[0];
        }
    }
}

