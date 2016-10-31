<?php

include_once "DAL.php";
include_once "MySQLConnectionInfo.php";

class DALMySQL extends DAL
{
    private $connectionInfo = null;
    private $connection = null;
    private $logger = null;
    
    public function Connect( $info )
    {
        $this->connectionInfo = $info;
		$this->Close();
        $this->connection = @mysql_connect($info->getHost(), $info->getUser(), $info->getPassword(), true);
        if ( $this->connection === false ) {
        	throw new Exception(mysql_error()); 
        }
        if ( !mysql_select_db($info->getDbName(), $this->connection) ) {
        	throw new Exception(mysql_error());
        }
        @mysql_query("SET time_zone = '".EnvironmentSettings::getUTCOffset().":00'", $this->connection);
        @mysql_query("SET NAMES '".APP_CHARSET."' COLLATE '".APP_CHARSET."_general_ci'", $this->connection);
    }
    
    public function Reconnect()
    {
    	if ( !@mysql_ping($this->connection) ) {
	    	$this->Connect( $this->connectionInfo );
    	}
    }

    function __destruct()
    {
    	$this->Close();
    }

    public function QueryAllRows( $sql )
    {
        if ( is_resource($sql) ) return mysql_fetch_all($sql, MYSQLI_BOTH);
        return mysql_fetch_all($this->Query($sql), MYSQLI_BOTH);
    }

    public function QueryAssocArray($sql)
    {
        if ( is_resource($sql) ) {
            $result = mysql_fetch_assoc($sql);
        }
        else {
            $result = mysql_fetch_assoc($this->Query($sql));
        }
        return is_null($result) ? array() : $result;
    }

    public function QueryArray($sql)
    {
        if ( is_resource($sql) ) {
            $result = mysql_fetch_array($sql);
        }
        else {
            $result = mysql_fetch_array($this->Query($sql));
        }
        return is_null($result) ? array() : $result;
    }

    public function Query( $sql )
    {
        if ( is_null($sql) || !is_string($sql) ) return @mysql_query("SELECT 0", $this->connection);
        $this->info( $sql );
        
        $resultSet = @mysql_query($sql, $this->connection);

        if ( $resultSet === false )
        {
        	$this->Reconnect();
        	
        	$resultSet = @mysql_query($sql, $this->connection);
        	
        	if ( $resultSet === false ) throw new Exception(mysql_error().': '.$sql);
        }
        
        return $resultSet;
    }

    public function Seek( &$result, $offset )
    {
        mysql_data_seek($result, $offset);
    }

    public function RowsNum( $result )
    {
        return mysql_num_rows($result);
    }

    public function Escape( $sql_string )
    {
        return @mysql_real_escape_string($sql_string);
    }
    
    public function GetAffectedRows()
    {
        return mysql_affected_rows($this->connection);
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

    protected function Close()
    {
       	if ( is_resource($this->connection) ) {
	    	mysql_close($this->connection);
	    	$this->connection = null;
       	}
    }
}