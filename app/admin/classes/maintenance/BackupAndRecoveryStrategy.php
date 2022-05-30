<?php

class BackupAndRecoveryStrategy
{
 	var $file_name;
 	var $backup_name;
 	
 	private $log;
 	
 	function __construct() 
 	{
 	}
 	
 	function setBackupName( $name )
 	{
 		$this->backup_name = $name;
 	}
 	
 	function getBackupName() 
 	{
 		if($this->backup_name == '') {
			$this->backup_name = 'backup.'.strftime('%Y.%m.%d.%H.%M', time()).'.'.EnvironmentSettings::getServerName().'.'.md5(INSTALLATIONU_UID.time());
		}
 		return $this->backup_name;
 	}

 	function getBackupFileName() 
 	{
 		if($this->file_name == '') $this->file_name = $this->getBackupName().'.zip'; 
 		return $this->file_name;
 	}

 	function backup_database()
 	{
 		if ( !is_dir(SERVER_BACKUP_PATH) ) mkdir(SERVER_BACKUP_PATH);
 		
 		$sql_path = SERVER_BACKUP_PATH.'devprom/';
 		
 		if ( !is_dir($sql_path) )
 		{
			$this->writeLog("Backup: make directory ".$sql_path);
 			
			mkdir($sql_path, 0700, true);
 		}
		
		if ( defined('MYSQL_BACKUP_COMMAND') )
		{
			$command = str_replace('%1', DB_HOST, 
				str_replace('%2', DB_USER, 
					str_replace('%3', DB_PASS, 
						str_replace('%4', DB_NAME, 
							str_replace('%5', $sql_path.'devprom.sql', MYSQL_BACKUP_COMMAND ) ) ) ) );
			
		}
		else
		{
			$command = 'mysqldump --set-charset --default-character-set='.APP_CHARSET.' ' .
				' --host='.DB_HOST.' --port=3306 --user='.DB_USER.' --password='.DB_PASS.
				' --add-drop-table --force '.DB_NAME.' > '.
				$sql_path.'devprom.sql';
		}

		$this->writeLog("Backup: ".$command);

        try {
            $this->writeLog('Backup: ' . \FileSystem::execAndSendResponse($command));
            $this->configure_database_file( $sql_path.'devprom.sql' );
        }
        catch( \Exception $e ) {
            $this->errorLog("Backup: ".$e->getMessage());
            throw $e;
        }
 	}
 	
 	function configure_database_file( $path )
 	{
 		$dbf = fopen( $path, 'r+');
 		
 		if ( $dbf === false )
 		{
			$this->writeLog("Backup: unable find database dump file ".$path);
			
 			return;
 		}
 		
 		$strings = array (
 			"",
 			"DROP DATABASE IF EXISTS ".DB_NAME.";",
			"SET character_set_server=".APP_CHARSET.";",
			"SET character_set_database=".APP_CHARSET.";",
			"SET collation_database=".APP_CHARSET."_general_ci;",
			"SET NAMES '".APP_CHARSET."' COLLATE '".APP_CHARSET."_general_ci';",
			"SET CHARACTER SET ".APP_CHARSET.";",
			"CREATE DATABASE ".DB_NAME.";",
			"USE ".DB_NAME.";",
 			""
		);
 		
 		$data_modified = false;
 		
 		while ( !feof($dbf) )
 		{
 			$line = fgets( $dbf, 1024 );
 			
 			if ( strpos($line, '/*!') !== false )
 			{
 				fseek($dbf, -strlen($line), SEEK_CUR);
 				
 				if ( !$data_modified )
 				{
	 				foreach ( $strings as $string )
	 				{
	 					fwrite( $dbf, $string.chr(10) );
	 				}
	 				
	 				fwrite ( $dbf, '-- ');
	 				
	 				$data_modified = true;
 				}
 				else
 				{
 					fwrite( $dbf, preg_replace('/\/\*\!/', '-- ', $line) );
 				}
 			}

 			if ( stripos($line, 'drop table') !== false ) break;
 		}
 		
 		fseek($dbf, -500, SEEK_END);

 	 	while ( !feof($dbf) )
 		{
 			$line = fgets( $dbf, 1024 );
 			
 			if ( strpos($line, '/*!') !== false )
 			{
 				fseek($dbf, -strlen($line), SEEK_CUR);
 				
 				fwrite( $dbf, preg_replace('/\/\*\!/', '-- ', $line) );
 			}
 		}
 		
 		fclose( $dbf );
 	}

 	function backup_htdocs() 
 	{
 		if ( is_dir(SERVER_BACKUP_PATH) === false ) {
 			$this->writeLog("Backup: make directory ".SERVER_BACKUP_PATH);
 			mkdir(SERVER_BACKUP_PATH, 0700, true);
 		}

		$this->writeLog("Backup: start copying application");
		
		$this->full_copy( DOCUMENT_ROOT, SERVER_BACKUP_PATH, false );
        unlink( SERVER_BACKUP_PATH.'htdocs/settings_server.php' );

 		$this->writeLog("Backup: application copying passed");
 	}
 	
 	function backup_files() 
 	{
 		if ( !is_dir(SERVER_BACKUP_PATH) ) {
             mkdir(SERVER_BACKUP_PATH);
        }

		// files backup destination
		$files_backup_path = SERVER_BACKUP_PATH . $this->getBackupName().'/';
		if ( !is_dir($files_backup_path) ) {
			$this->writeLog("Backup: make directory ".$files_backup_path);
			mkdir($files_backup_path, 0700, true);
		}

		$this->full_copy( SERVER_FILES_PATH . '/.', $files_backup_path, true );
 	}

 	function recovery_unzip( $backup_file_name ) 
 	{
 		$this->writeLog("Recovery: unzip backup archive");
 		$this->unzip( SERVER_BACKUP_PATH, $backup_file_name );
 	} 

	function recovery_database() 
	{
 		$this->writeLog("Recovery: restore database");
		
 		$sql_path = SERVER_BACKUP_PATH.'devprom/devprom.sql';
 		
 		$host_parts = preg_split('/:/', DB_HOST);
 		
		if ( defined('MYSQL_APPLY_COMMAND') )
		{
			$command = str_replace('%1', $host_parts[0], 
				str_replace('%2', DB_USER, 
					str_replace('%3', DB_PASS, 
						str_replace('%4', DB_NAME, 
							str_replace('%5', $sql_path, MYSQL_APPLY_COMMAND ) ) ) ) );
		}
		else
		{
			$command = 'mysql --host='.$host_parts[0].' --port='.$host_parts[1].' --user='.DB_USER.' --password='.DB_PASS.
				' --database='.DB_NAME.' -e "source '.$sql_path.'" 2>&1';
		}

		$this->writeLog($command);

        try {
            $this->writeLog('Recovery: ' . \FileSystem::execAndSendResponse($command));
        }
        catch( \Exception $e ) {
            $this->errorLog("Recovery: ".$e->getMessage());
            throw $e;
        }
	}
	
 	function recovery_files( $backup_file_name ) 
 	{
 		$this->writeLog("Recovery: restore attachments");
 		
 		$parts = pathinfo($backup_file_name);
		
		// files backup destination
		$files_backup_path = SERVER_BACKUP_PATH.
			preg_replace('/\.zip/i', '', $parts['basename']).'/';
		
		$this->full_copy( $files_backup_path . '/.', SERVER_FILES_PATH, true );
 	}

	function recovery_htdocs() 
	{
 		$this->writeLog("Recovery: restore application");
		
		if ( !file_exists(SERVER_BACKUP_PATH.'htdocs/common.php') ) {
			return text(1052).' '.SERVER_BACKUP_PATH.'htdocs/common.php';	
		}

        \FileSystem::rmdirr( DOCUMENT_ROOT.'ext/' );
		$this->full_copy( SERVER_BACKUP_PATH.'htdocs', dirname(DOCUMENT_ROOT), false );
		
		return '';
	}

	function recovery_clean($backup_file_name) 
	{
        \FileSystem::rmdirr( SERVER_BACKUP_PATH.'devprom/' );
        \FileSystem::rmdirr( SERVER_BACKUP_PATH.'htdocs/' );

		$parts = pathinfo($backup_file_name);
		
		// files backup destination
		$files_backup_path = SERVER_BACKUP_PATH.
			$parts['basename'].'/';

        \FileSystem::rmdirr( $files_backup_path );
	}

 	function update_unzip( $update_file_name ) 
 	{
 		$this->writeLog("UNZIP archive: ".$update_file_name."\n");

		// remove old directories
		$this->update_clean();

		// make main directories
		if ( !is_dir(SERVER_UPDATE_PATH.'devprom') ) mkdir(SERVER_UPDATE_PATH.'devprom');
		if ( !is_dir(SERVER_UPDATE_PATH.'htdocs') ) mkdir(SERVER_UPDATE_PATH.'htdocs');

 		return $this->unzip( SERVER_UPDATE_PATH, $update_file_name );
 	} 

	function update_database() 
	{
 		$sql_path = SERVER_UPDATE_PATH.'devprom/update.sql';

 		$this->writeLog("UPDATE database\n");

		$file_content = file_get_contents($sql_path); 
		
		if ( $file_content == '' )
		{
			$this->writeLog(text(1031).': '.$sql_path."\n");
			return;
		}

		// setup specific statements in the update script 		
		if ( defined('MYSQL_UPDATE_COMMAND') )
		{
			$command = str_replace('%1', DB_HOST, 
				str_replace('%2', DB_USER, 
					str_replace('%3', DB_PASS, 
						str_replace('%4', DB_NAME, 
							str_replace('%5', $sql_path, MYSQL_UPDATE_COMMAND ) ) ) ) );
		}
		else
		{
			$command = 'mysql --host='.DB_HOST.' --user='.DB_USER.' --password='.DB_PASS.
				' --database='.DB_NAME.' -e "source '.$sql_path.'" 2>&1';
		}

        $this->writeLog($command);
        try {
            $this->writeLog('Update: ' . \FileSystem::execAndSendResponse($command));
        }
        catch( \Exception $e ) {
            $this->errorLog("Update: ".$e->getMessage());
            throw $e;
        }
	}
	
	function update_htdocs() 
	{
		$this->writeLog("UPDATE code\n");
		$this->full_copy(
            SERVER_UPDATE_PATH.'htdocs',
            dirname(DOCUMENT_ROOT),
            false
        );

        if ( is_dir(SERVER_UPDATE_PATH.'htdocs/ext') ) {
            \FileSystem::rmdirr( DOCUMENT_ROOT.'ext/' );
            $this->full_copy(
                SERVER_UPDATE_PATH.'htdocs/ext',
                DOCUMENT_ROOT,
                false
            );
        }
	}

	function update_getinfo( &$update_num ) 
	{
		$file_path = SERVER_UPDATE_PATH.'devprom/version.txt';
		if ( file_exists($file_path) )
		{ 
			$f = fopen( $file_path, "r" );
			$update_num = fread($f, filesize($file_path));
		 	fclose($f);
		}

		$this->writeLog("VERSION: ".$update_num."\n");
	}

	function update_getrequired( &$update_num ) 
	{
		$file_path = SERVER_UPDATE_PATH.'devprom/required.txt';
		if ( file_exists($file_path) )
		{ 
			$update_num = file_get_contents( $file_path );
		}

		$this->writeLog("REQUIRED: ".$update_num."\n");
	}
	
	function update_clean() 
	{
        \FileSystem::rmdirr( SERVER_UPDATE_PATH.'devprom/' );
        \FileSystem::rmdirr( SERVER_UPDATE_PATH.'htdocs/' );
	}

 	function zip() 
 	{
 		$command = defined('ZIP_APPEND_COMMAND')
 			? ZIP_APPEND_COMMAND : 'nohup zip -rm %1 %2 %3 & ';

 		chdir( SERVER_BACKUP_PATH );
 		
 		$command = str_replace('%1', $this->getBackupFileName(),
 			str_replace('%2', 'devprom', str_replace('%3', 'htdocs', $command ) ) 
 			);
 				
		$this->writeLog("Zip: ".$command);

        try {
            $this->writeLog('Zip: ' . \FileSystem::execAndSendResponse($command));
            if ( strpos($command, '-rm') === false ) {
                // remove source backup files manually if no 'm' flag
                \FileSystem::rmdirr( SERVER_BACKUP_PATH.'devprom/' );
                \FileSystem::rmdirr( SERVER_BACKUP_PATH.'htdocs/' );
            }
        }
        catch( \Exception $e ) {
            $this->errorLog("Zip: ".$e->getMessage());
            throw $e;
        }
 	}

 	function unzip( $zip_file_directory, $zip_file_name )
 	{
		chdir($zip_file_directory);

		if( preg_match('/[\\\\;:\\/{}()\s]+/', $zip_file_name ) > 0 ) {
			 return text(1056);
		}
		
 		if ( defined('UNZIP_COMMAND') ) {
 			$command = str_replace('%1', $zip_file_name, UNZIP_COMMAND );
 		}
 		else {
 			$command = 'unzip -q '.$zip_file_name;
 		}

        $this->writeLog($command."\n");
        try {
            $this->writeLog('Unzip: ' . \FileSystem::execAndSendResponse($command));
        }
        catch( \Exception $e ) {
            $this->errorLog("Unzip: ".$e->getMessage());
            throw $e;
        }
 	}
 	
 	function full_copy( $source_path, $destination_path, $async = false )
 	{
        if ( $async ) {
            $command = "nohup cp -rf {$source_path} {$destination_path} & ";
        } else {
            $command = "cp -rf {$source_path} {$destination_path} ";
        }

        $this->writeLog($command."\n");
        try {
            $this->writeLog(\FileSystem::execAndSendResponse($command));
        }
        catch( \Exception $e ) {
            $this->errorLog($e->getMessage());
            throw $e;
        }
 	}

	function writeLog( $message )
	{
	 	try 
 		{
 			if ( !is_object($this->log) ) 
 			{
 				$this->log = Logger::getLogger('Install'); 
 			}
 			
 			$this->log->info( trim($message, chr(10).chr(13)) );
 		}
 		catch( Exception $e)
 		{
 			error_log('Unable initialize logger: '.$e->getMessage());
 		}
	}

    function errorLog( $message )
    {
        try {
            if ( !is_object($this->log) ) {
                $this->log = Logger::getLogger('Install');
            }
            $this->log->error( trim($message, chr(10).chr(13)) );
        }
        catch( Exception $e) {
            error_log('Unable initialize logger: '.$e->getMessage());
        }
    }
}