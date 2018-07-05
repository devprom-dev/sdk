<?php

class CheckpointApacheModuleLoaded extends CheckpointEntryDynamic
{
    var $module;

    function CheckpointApacheModuleLoaded( $module )
    {
        $this->module = $module;
    }

    function getUid()
    {
        return md5(parent::getUid().$this->module);
    }

    function execute()
    {
        if ( $this->checkWindows() ) {
            $this->setValue( '1' ); return;
        }
        if ( !function_exists('apache_get_modules') ) {
            $this->setValue( '1' ); return;
        }
        $this->setValue( in_array(
                $this->module, apache_get_modules() ) ? '1' : '0' );
    }

    function getTitle()
    {
        return 'Apache: '.$this->module;
    }

    function getDescription()
    {
        return text(1134);
    }
}