<?php

class UpgradeMySQL extends Installable 
{
    function skip()
    {
        $version = $this->getMySQLVersion();
        
        $this->info('MySQL version is ' . $version);
        
        return !$this->checkWindows() || $version > '5.1' || !is_dir(SERVER_ROOT_PATH.'templates/config/mysql');
    }

    // checks all required prerequisites
    function check()
    {
    	return true;
    }

    function install()
    {
        $this->executeCommand( $this->getStopMySQLService() );
              
        $this->copyFile( 'bin/libmysql.dll' );
        $this->copyFile( 'bin/mysql.exe' );
        $this->copyFile( 'bin/mysql_upgrade.exe' );
        $this->copyFile( 'bin/mysqladmin.exe' );
        $this->copyFile( 'bin/mysqlcheck.exe' );
        $this->copyFile( 'bin/mysqld.exe', 'bin/mysqld-nt.exe' );
        $this->copyFile( 'bin/mysqldump.exe' );
        $this->copyFile( 'share/english/errmsg.sys' );
        $this->copyFile( 'share/russian/errmsg.sys' );
        $this->copyFile( 'data/plugin.frm' );
        $this->copyFile( 'data/plugin.myd' );
        $this->copyFile( 'data/plugin.myi' );
        $this->copyFile( 'my.ini' );
        
        $this->upgradeMyIniFile($this->getPortNumber());
        
        $this->executeCommand( $this->getStartMySQLService() );
        
        $this->executeCommand( $this->getUpgradeExecutable($this->getPortNumber()) );
        
        return true;
    }
    
    function getPortNumber()
    {
        list($db_host, $db_port) = preg_split('/:/', DB_HOST);

        if ( $db_port == '' ) $db_port = '3306';
        
        return $db_port;
    }
    
    function copyFile( $file_name, $target_file_name = '' )
    {
        $source_file = SERVER_ROOT_PATH.'templates/config/mysql/'.$file_name;
        
        $local_file = SERVER_ROOT.'/mysql/'.($target_file_name == '' ? $file_name : $target_file_name);
                
        $backup_dir = SERVER_ROOT.'/mysql/backup/';
        
        if ( !is_dir($backup_dir) ) mkdir($backup_dir);
        
        $backup_file = $backup_dir.($target_file_name == '' ? $file_name : $target_file_name);
        
        $this->info( 'Copy file '.$source_file.' to '.$local_file );
	    
        if ( !is_dir(dir($backup_file)) ) mkdir(dir($backup_file), 0777, true);
        
    	if ( !@copy($local_file, $backup_file) )
	    {
	        $this->error( var_export(error_get_last(), true) );
	    }
	    
	    if ( !@copy($source_file, $local_file) )
	    {
	        $this->error( var_export(error_get_last(), true) );
	    }
    }
    
    function executeCommand( $command )
    {
        $this->info('Executing: ' . $command);
        
        exec($command, $output, $retCode);
        
        $this->info('Result: ' . $retCode . ', Output: ' . var_export($output, true));
    }

    function getStopMySQLService()
    {
        return "net stop DEVPROM.MySQL";
    }
    
    function getStartMySQLService()
    {
        return "net start DEVPROM.MySQL";
    }
    
    function getUpgradeExecutable( $port_number )
    {
        return SERVER_ROOT . '/mysql/bin/mysql_upgrade --port='.$port_number;
    }
    
    function upgradeMyIniFile( $port_number )
    {
        $content = file_get_contents(SERVER_ROOT . '/mysql/my.ini');
        
        $content = str_replace('%MYSQL_PORT%', $port_number, $content);
        
        $content = str_replace('%key_buffer%', '1024M', $content);
        
        $content = str_replace('%table_cache%', '128M', $content);

        $content = str_replace('?INSTALLDIR', SERVER_ROOT, $content);
        
        file_put_contents(SERVER_ROOT . '/mysql/my.ini', $content);
    }
    
    function getMySQLVersion()
    {
        return array_shift(DAL::Instance()->QueryArray('SELECT VERSION()'));
    }
}
