<?php

abstract class Lock
{
    abstract function __construct( $name );
    
    abstract function Lock();
    
    abstract function Release();

    abstract function Locked( $timeout );
}