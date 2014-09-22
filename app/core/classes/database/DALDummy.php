<?php

include_once "DAL.php";

class DALDummy extends DAL
{
    public function Connect( $parms )
    {
    }
    
    public function Query( $sql )
    {
        return array();
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
