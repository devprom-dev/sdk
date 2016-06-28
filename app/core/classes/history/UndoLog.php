<?php

class UndoLog
{
    public static function Instance()
    {
        if ( is_object(static::$singleInstance) ) return static::$singleInstance;
        static::$singleInstance = new static();
        return static::$singleInstance;
    }

    public function put( $object_it )
    {
        if ( $object_it->object->getEntityRefName() == 'ObjectChangeLog' ) return;
        $class_name = strtolower(get_class($object_it->object));
        if ( $class_name == 'metaobject' ) {
            $class_name = $object_it->object->getClassName();
        }
        $this->queue[$class_name][] = $object_it->copy()->serialize2Xml();
    }

    public function getPath( $transaction ) {
        if ( preg_match('/[a-z0-9A-Z]+/i', $transaction) < 1 ) return '';
        return $this->dirPath . '/' . $transaction;
    }

    public function getDirectory() {
        return $this->dirPath;
    }

    function __destruct()
    {
        if ( count($this->queue) < 1 ) return;

        $xml = '<?xml version="1.0" encoding="'.APP_ENCODING.'"?><entities>';
        foreach( $this->queue as $className => $objects ) {
            $xml .= '<entity class="'.$className.'" encoding="'.APP_ENCODING.'">';
            foreach( $objects as $objectXml ) {
                $xml .= $objectXml;
            }
            $xml .= '</entity>';
        }
        $xml .= '</entities>';
        file_put_contents($this->filePath, $xml);
    }

    protected function __construct() {
        $this->dirPath = SERVER_FILES_PATH.'undo';
        if ( !is_dir($this->dirPath)) {
            mkdir($this->dirPath);
        }
        $this->filePath = $this->getPath(ChangeLog::getTransaction());
    }

    protected static $singleInstance = null;
    private $dirPath = '';
    private $filePath = '';
    private $queue = array();
}