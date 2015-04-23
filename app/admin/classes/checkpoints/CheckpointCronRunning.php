<?php

class CheckpointCronRunning extends CheckpointEntryStatic
{
    function getValue()
    {
        $info_path = DOCUMENT_ROOT.'conf/runtime.info';

        if ( !file_exists($info_path) ) return '';

        $file = fopen( $info_path, 'r', 1 );
        
        $result = fread( $file, filesize($info_path) );
        
        fclose( $file );

        return $result;
    }

    function check()
    {
        $value = $this->getValue();

        return $value > 0 && ( time() - $value < 3600 );
    }

    function getTitle()
    {
        return text(1156);
    }

    function getDescription()
    {
        return $this->check() ? text(1930) : text(1872);
    }
}