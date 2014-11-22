<?php

abstract class DAL
{
    protected static $singleInstance = null;
    
    public abstract function Connect( $info );
    
    public abstract function Reconnect();
    
    public abstract function Query( $sql );
    
    public abstract function GetAffectedRows();
    
    public abstract function Escape( $sql_string );
    
    public static function Instance()
    {
        if ( is_object(static::$singleInstance) ) return static::$singleInstance;
        
        static::$singleInstance = new static();

        return static::$singleInstance;
    }
    
    private function __construct() {}
}