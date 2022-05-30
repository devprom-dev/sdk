<?php
include "BackupAndRecoveryStrategy.php";

class BackupAndRecoveryOnWindows extends BackupAndRecoveryStrategy
{
	var $database_path;
	
	function __construct() 
	{
		parent::__construct();
		
		$this->database_path = SERVER_CORPDB_PATH;
	}

 	function zip() 
 	{
		$archiver_path = SERVER_ROOT.'/tools/7za.exe';
        chdir(SERVER_BACKUP_PATH);

        $command = $archiver_path.' a -r -tzip '.$this->getBackupFileName().' devprom/* htdocs/*';
        $this->writeLog("Zip: ".$command);

        try {
            $this->writeLog('Zip: ' . \FileSystem::execAndSendResponse($command));
        }
        catch( \Exception $e ) {
            $this->errorLog(IteratorBase::wintoutf8($e->getMessage()));
            throw $e;
        }

		// remove source backup files
        \FileSystem::rmdirr( SERVER_BACKUP_PATH.'devprom/' );
        \FileSystem::rmdirr( SERVER_BACKUP_PATH.'htdocs/' );
 	}

 	function unzip( $zip_file_directory, $zip_file_name ) 
 	{
		if( preg_match('/[\\\\;:\\/{}()\s]+/', $zip_file_name ) > 0 )
		{
			 return text(1056);
		}
		
		$archiver_path = SERVER_ROOT.'/tools/7za.exe';
		if ( file_exists($archiver_path) )
		{
			chdir($zip_file_directory);
			$command = $archiver_path.' x -tzip -y '.$zip_file_directory.$zip_file_name;
			
			$this->writeLog($command);
            try {
                $this->writeLog('Unzip: ' . \FileSystem::execAndSendResponse($command));
            }
            catch( \Exception $e ) {
                $this->errorLog("Unzip: ".$e->getMessage());
                throw $e;
            }
		}
		else
		{
			return $this->unzip_old( $zip_file_directory, $zip_file_name );
		}
 	}
 	
 	function unzip_old( $zip_file_directory, $zip_file_name ) 
 	{
 		$this->writeLog('unzip '.$zip_file_directory.$zip_file_name.Chr(10));

		$zip = zip_open($zip_file_directory.$zip_file_name);
		if ($zip) 
		{
		    while ($zip_entry = zip_read($zip)) {
		        if (zip_entry_open($zip, $zip_entry, "r")) 
		        {
		        	if(zip_entry_filesize($zip_entry) == 0 &&
		        		strpos(zip_entry_name($zip_entry), ".") === false) 
		        	{
		        		$newpath = $zip_file_directory.zip_entry_name($zip_entry);
		        		if ( !is_dir($newpath) ) $result = mkdir($newpath);

		        		$this->writeLog(($result ? 'Ok. ' : 'Failed. ').'mkdir: '.
                   				$zip_file_directory.zip_entry_name($zip_entry).Chr(10));
		        	}
		            zip_entry_close($zip_entry);
		        }
		    }
		    zip_close($zip);
		}
		else
		{
			$this->writeLog('unable open zip'.$zip_file_directory.$zip_file_name.Chr(10));
   	 		return;
		}
		
		$zip = zip_open($zip_file_directory.$zip_file_name);
		if ($zip) 
		{
		    while ($zip_entry = zip_read($zip)) {
		        if (zip_entry_open($zip, $zip_entry, "r")) 
		        {
		        	if(zip_entry_filesize($zip_entry) != 0 ||
		        		strpos(zip_entry_name($zip_entry), ".") !== false) 
		        	{
		        		
						$zip_entry_file = fopen( $zip_file_directory.zip_entry_name($zip_entry), "w" );
						$result = fwrite( $zip_entry_file, zip_entry_read($zip_entry, zip_entry_filesize($zip_entry)) );

						$this->writeLog(($result < 1 ? 'Failed. ' : 'Ok. ').'extract: '.
                   				$zip_file_directory.zip_entry_name($zip_entry).Chr(10));

						fclose($zip_entry_file);
		        	}
		            zip_entry_close($zip_entry);
		        }
		    }
		    zip_close($zip);
		}

		$this->writeLog($zip_file_directory.$zip_file_name.' has been unzipped.'.Chr(10));
 	}
	
 	function backup_database() 
 	{
 		if ( is_dir(SERVER_BACKUP_PATH) === false )	mkdir(SERVER_BACKUP_PATH);

 		$sql_path = SERVER_BACKUP_PATH.'devprom/';
 		if ( is_dir($sql_path) === false ) mkdir($sql_path);
		
 		chdir(SERVER_ROOT.'/mysql/bin');
		$host_parts = preg_split('/:/', DB_HOST);
		if ( $host_parts[1] == '' ) $host_parts[1] = '3306';
		
		$command = 'mysqldump.exe --host='.$host_parts[0].' --port='.$host_parts[1].
			' --user='.DB_USER.' --password='.DB_PASS.' --add-drop-table --set-charset --force' .
			' --result-file="'.$sql_path.'devprom.sql'.
		    '" --default-character-set='.APP_CHARSET.' --character-sets-dir="'.
		    SERVER_ROOT.'/mysql/share/charsets" '.DB_NAME;
		
		$this->writeLog("Backup: ".$command);

		try {
            $this->writeLog("Backup: ".\FileSystem::execAndSendResponse($command));
        }
        catch( \Exception $e ) {
            $this->errorLog(IteratorBase::wintoutf8($e->getMessage()));
            throw $e;
        }

		$this->configure_database_file( $sql_path.'devprom.sql' );
		
		return true;
 	}

	function recovery_database() 
	{
 		$sql_path = SERVER_BACKUP_PATH.'devprom/devprom.sql';
 		
 		chdir(SERVER_ROOT.'/mysql/bin');
 		$host_parts = preg_split('/:/', DB_HOST);

        $command = 'mysql.exe --host='.$host_parts[0].' --port='.$host_parts[1].' --user='.DB_USER.' --password='.DB_PASS.
            ' --database='.DB_NAME.' -e "source '.$sql_path.'" ';

        try {
            $this->writeLog('Recovery: ' . \FileSystem::execAndSendResponse($command));
        }
        catch( \Exception $e ) {
            $this->errorLog(IteratorBase::wintoutf8($e->getMessage()));
            throw $e;
        }
	}

	function update_database() 
	{
 		$sql_path = SERVER_UPDATE_PATH.'devprom/update.sql';

 		$this->writeLog("UPDATE database\n");

		list($host, $port) = preg_split('/:/', DB_HOST);
		if ( $port == '' ) $port = '3306';
		
		if ( defined('MYSQL_UPDATE_COMMAND') )
		{
		    $command = str_replace('%1', $host,
		            str_replace('%2', DB_USER,
		                    str_replace('%3', DB_PASS,
		                            str_replace('%4', DB_NAME,
		                                    str_replace('%5', $sql_path, 
		                                            str_replace('%6', $port, MYSQL_UPDATE_COMMAND ) ) ) ) ) );
		}
		else
		{
		    $command = 'mysql --host='.$host.' --port='.$port.' --user='.DB_USER.' --password='.DB_PASS.
		        ' --database='.DB_NAME.' -e "source '.$sql_path.'"';
		}
		
		$command = preg_replace('/mysql/i', 'call "'.SERVER_CORPMYSQL_PATH.'"', $command);

        $this->writeLog($command);
        try {
            $this->writeLog('Update: ' . \FileSystem::execAndSendResponse($command));
        }
        catch( \Exception $e ) {
            $this->errorLog(IteratorBase::wintoutf8($e->getMessage()));
            throw $e;
        }
	}

    function full_copy( $source_path, $destination_path, $async = false )
    {
        if ( substr($source_path, -1) == '.' ) {
            $source_path = rtrim($source_path, '\\/.');
        }
        else {
            $destination_path = rtrim($destination_path, '\\/') . '/' . basename($source_path);
            mkdir($destination_path, 0777, true);
        }

        $source_path = realpath($source_path);
        $destination_path = realpath($destination_path);

        $command = "xcopy /s/e/h/y \"{$source_path}\" \"{$destination_path}\" ";

        $this->writeLog($command."\n");
        try {
            $this->writeLog(\FileSystem::execAndSendResponse($command));
        }
        catch( \Exception $e ) {
            $this->errorLog(IteratorBase::wintoutf8($e->getMessage()));
            throw $e;
        }
    }
}