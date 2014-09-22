<?php

include_once "DAL.php";
include "MySQLConnectionInfo.php";

class DALMySQL extends DAL
{
    private $connectionInfo = null;
    private $connection = null;
    private $logger = null;
    
    public function Connect( $info )
    {
        $this->connectionInfo = $info;
        
        $this->connection = mysql_connect($info->getHost(), $info->getUser(), $info->getPassword(), true) 
            or trigger_error(mysql_error(), E_USER_ERROR);
        
        mysql_select_db($info->getDbName(), $this->connection) or trigger_error(mysql_error(), E_USER_ERROR);
 
        mysql_query("SET time_zone = '".EnvironmentSettings::getUTCOffset().":00'", $this->connection) or trigger_error(mysql_error(), E_USER_ERROR);
        
        mysql_query("SET NAMES 'cp1251' COLLATE 'cp1251_general_ci'", $this->connection) or trigger_error(mysql_error(), E_USER_ERROR);
    }
    
    public function Query( $sql )
    {
        if ( ! @mysql_ping($this->connection) ) $this->Connect( $this->connectionInfo );

        $this->info( $sql );
        
        $resultSet = mysql_query($sql, $this->connection);

        if ( $resultSet === false ) throw new Exception(mysql_error().': '.$sql);
        
        return $resultSet;
    }
     
    public function Escape( $sql_string )
    {
        if ( ! @mysql_ping($this->connection) ) return $sql_string;

        return mysql_real_escape_string($sql_string);
    }
    
    public function GetAffectedRows()
    {
        return mysql_affected_rows();
    }

    protected function getLogger()
    {
   		try 
 		{
 			if ( !is_object($this->logger) && class_exists('Logger') )
 			{
 				$this->logger = Logger::getLogger('System');
 			}
 			
 			return $this->logger;
 		}
 		catch( Exception $e)
 		{
 			error_log('Unable initialize logger: '.$e->getMessage());
 		}
    }

    protected function info( $message )
    {
    	$log = $this->getLogger();
		
		if ( !is_object($log) ) return;
		
		$log->info( $message );
    }
}