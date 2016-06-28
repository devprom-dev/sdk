<?php

include_once "DAL.php";
include_once "MySQLConnectionInfo.php";

class DALMySQLi extends DAL
{
    private $connectionInfo = null;
    private $connection = null;
    private $logger = null;
    
    public function Connect( $info )
    {
        $this->connectionInfo = $info;
		$this->Close();
        $this->connection = @mysqli_connect($info->getHost(), $info->getUser(), $info->getPassword(), $info->getDbName());
        if ( $this->connection === false ) {
        	throw new Exception(mysqli_connect_error());
        }
        @mysqli_query($this->connection, "SET time_zone = '".EnvironmentSettings::getUTCOffset().":00'");
        @mysqli_query($this->connection, "SET NAMES '".APP_CHARSET."' COLLATE '".APP_CHARSET."_general_ci'");
    }
    
    public function Reconnect()
    {
    	if ( !@mysqli_ping($this->connection) ) {
	    	$this->Connect( $this->connectionInfo );
    	}
    }

    function __destruct()
    {
    	$this->Close();
    }

    public function QueryAllRows( $sql )
    {
        if ( $sql instanceof \mysqli_result ) return mysqli_fetch_all($sql, MYSQLI_BOTH);
        return mysqli_fetch_all($this->Query($sql), MYSQLI_BOTH);
    }

    public function QueryAssocArray($sql)
    {
        if ( $sql instanceof \mysqli_result ) {
            $result = mysqli_fetch_assoc($sql);
        }
        else {
            $result = mysqli_fetch_assoc($this->Query($sql));
        }
        return is_null($result) ? array() : $result;
    }

    public function QueryArray($sql)
    {
        if ( $sql instanceof \mysqli_result ) {
            $result = mysqli_fetch_array($sql);
        }
        else {
            $result = mysqli_fetch_array($this->Query($sql));
        }
        return is_null($result) ? array() : $result;
    }

    public function Query( $sql )
    {
        $this->info( $sql );
        
        $resultSet = @mysqli_query($this->connection, $sql);
        if ( $resultSet === false ) {
        	$this->Reconnect();
        	
        	$resultSet = @mysqli_query($this->connection, $sql);
        	if ( $resultSet === false ) throw new Exception(mysqli_error($this->connection).': '.$sql);
        }

        return $resultSet;
    }

    public function Seek( &$result, $offset )
    {
        return mysqli_data_seek($result, $offset);
    }

    public function RowsNum( $result )
    {
        return mysqli_num_rows($result);
    }

    public function Escape( $sql_string )
    {
        return @mysqli_real_escape_string($this->connection, $sql_string);
    }
    
    public function GetAffectedRows()
    {
        return mysqli_affected_rows($this->connection);
    }

    protected function getLogger()
    {
   		try {
 			if ( !is_object($this->logger) && class_exists('Logger') ) {
 				$this->logger = Logger::getLogger('System');
 			}
 			return $this->logger;
 		}
 		catch( Exception $e) {
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
       	if ( is_object($this->connection) ) {
	    	mysqli_close($this->connection);
	    	$this->connection = null;
       	}
    }
}