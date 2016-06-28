<?php

abstract class DAL
{
    public abstract function Connect( $info );
    public abstract function Reconnect();
    public abstract function Query( $sql );
    public abstract function QueryAssocArray( $sql );
    public abstract function QueryArray( $sql );
    public abstract function QueryAllRows( $sql );
    public abstract function Seek( &$result, $offset );
    public abstract function RowsNum( $result );
    public abstract function GetAffectedRows();
    public abstract function Escape( $sql_string );
    
    public static function Instance()
    {
        if ( is_object(static::$singleInstance) ) return static::$singleInstance;
        static::$singleInstance = new static();
        return static::$singleInstance;
    }
    
    private function __construct() {}
    protected static $singleInstance = null;
}