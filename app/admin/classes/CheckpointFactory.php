<?php

include_once 'CheckpointBase.php';
include_once 'CheckpointEntryBase.php';
include_once 'CheckpointEntryStatic.php';
include_once 'CheckpointEntryDynamic.php';

class CheckpointFactory
{
    var $root;
    var $checkpoints;

    function CheckpointFactory( $root = '' )
    {
        $this->root = $root == '' ? SERVER_ROOT_PATH : $root;

        $this->checkpoints = array();
    }

    function & getCheckpoint( $class_name )
    {
        foreach( $this->checkpoints as $checkpoint )
        {
            if ( is_a ( $checkpoint, $class_name ) )
            {
                return $checkpoint;
            }
        }

        if ( !class_exists( $class_name, false ) )
        {
            include $this->root.'admin/classes/checkpoints/'.$class_name.'.php';
        }

        if ( !class_exists( $class_name, false ) )
        {
            return null;
        }
        
        $checkpoint = new $class_name;
        
        array_push( $this->checkpoints, $checkpoint );
        
        return $checkpoint;
    }
}

function getCheckpointFactory()
{
    global $checkpoint_factory;

    if ( !is_object($checkpoint_factory) )
    {
        $checkpoint_factory = new CheckpointFactory();
    }

    return $checkpoint_factory;
}
