<?php

class CheckpointExtentionLoaded extends CheckpointEntryDynamic
{
    var $extention;

    function CheckpointExtentionLoaded( $extention )
    {
        $this->extention = $extention;
    }

    function getUid()
    {
        return md5(parent::getUid().$this->extention);
    }

    function execute()
    {
        $this->setValue( extension_loaded( $this->extention ) ? '1' : '0' );
    }

    function getRequired()
    {
        return true;
    }
    
    function getTitle()
    {
        return 'PHP: '.$this->extention;
    }

    function getDescription()
    {
        return text(1132);
    }
}