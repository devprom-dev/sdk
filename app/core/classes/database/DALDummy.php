<?php

include_once "DAL.php";

class DALDummy extends DAL
{
    public function Connect( $parms )
    {
    }

    public function Reconnect()
    {
    }
    
    public function Query( $sql )
    {
        return array();
    }

    public function QueryAllRows( $sql )
    {
        return array();
    }

    public function QueryAssocArray( $sql )
    {
        return array();
    }

    public function QueryArray( $sql )
    {
        return array();
    }

    public function Seek( &$result, $offset ) {
    }

    public function RowsNum( $result ) {
        return 0;
    }

    public function Escape( $sql_string )
    {
        return $sql_string;
    }
    
    public function GetAffectedRows()
    {
        return 0;
    }
}
