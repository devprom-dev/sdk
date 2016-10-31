<?php

class InstallSystem extends CommandForm
{
	function validate()
	{
		global $_SERVER, $_REQUEST, $model_factory;

		$state = $model_factory->getObject('DeploymentState');
		
		if ( $state->IsInstalled() )
		{
			$this->replyError( text(1362) );
		}
		
		$this->checkRequired( array(
			'MySQLHost', 'Database', 'DatabaseUser'
		));

		// check disable_functions parameters
		$functions = array( 'shell_exec', 'exec', 'system' );
		
		if ( preg_match('/'.join('|',$functions).'/', ini_get('disable_functions')) )
		{
		    $this->replyError( str_replace('%1', join(', ',$functions), text(1297)) );
		}
		
		// check required utilities are in place
		if ( $_SERVER['WINDIR'] == '' )
		{
			$result = shell_exec( defined('ZIP_HELP_COMMAND') ? UNZIP_HELP_COMMAND : 'unzip --help');

			if ( $result == '' )
			{
				$this->replyError( str_replace('%1', 'unzip', text(1003)) );
			}

			$result = shell_exec( defined('UNZIP_HELP_COMMAND') ? ZIP_HELP_COMMAND : 'zip --help');
			
			if ( $result == '' )
			{
				$this->replyError( str_replace('%1', 'zip', text(1003)) );
			}

			$result = shell_exec( defined('MYSQLDUMP_HELP_COMMAND') ? MYSQLDUMP_HELP_COMMAND : 'mysqldump --help');
			
			if ( $result == '' )
			{
				$this->replyError( str_replace('%1', 'mysqldump', text(1003)) );
			}
		}

		return true;
	}

	function create()
	{
		$hostname = $this->utf8towin($_REQUEST['MySQLHost']);
		$dbname = $this->utf8towin($_REQUEST['Database']);
		$username = $this->utf8towin($_REQUEST['DatabaseUser']);
		$password = $this->utf8towin($_REQUEST['DatabasePass']);

		// check MySQL parameters
		try {
			$this->connect($hostname, $username, $password);
		}
		catch( \Exception $e ) {
			$this->replyError( text(1514).': '.$e->getMessage() );
		}

		// prepare database file
		$parts = pathinfo(__FILE__);
		$parts['dirname'] = dirname($parts['dirname']);

		$sql_script = $parts['dirname'].'/devprom.sql';

		if ( !file_exists($parts['dirname'].'/devprom.old.sql') )
		{
			copy($sql_script, $parts['dirname'].'/devprom.old.sql');
		}
		else
		{
			copy($parts['dirname'].'/devprom.old.sql', $sql_script);
		}

		// setup the language settings in the database
		$f = fopen($sql_script, 'r', 1);
		if($f === false)
		{
			$this->replyError( text(1031).': '.$sql_script );
		}

		$file_content = fread($f, filesize($sql_script));
		fclose($f);

		$f = fopen($sql_script, 'w', 1);
		if($f === false)
		{
			$this->replyError( text(1031).': '.$sql_script );
		}

		$file_content = str_replace("use devprom", "use ".$dbname, $file_content);

		$file_content = preg_replace("/analyze\s+table/mi", "-- analyze table", $file_content);

		$file_content = preg_replace("/optimize\s+table/mi", "-- optimize table", $file_content);
		
		if ( in_array('SkipCreation', array_keys($_REQUEST)) ) {
			$file_content = str_replace("create database devprom;", '', $file_content);
		}
		else {
			$file_content = str_replace("create database devprom;", 'CREATE DATABASE '.$dbname.';', $file_content);
		}

        if ( TextUtils::versionToString($this->getMySQLVersion()) >= TextUtils::versionToString('5.6') ) {
            $file_content = preg_replace('/engine\s*=\s*myisam/i', 'engine=innodb', $file_content);
        }

		fwrite($f, $file_content);
		fclose($f);

		if ( !in_array('SkipStructure', array_keys($_REQUEST)) )
		{
			if ( !function_exists('shell_exec') )
			{
				$this->replyError( text(1515).': '.text(992) );
			}

			// create database and its structure
			if ( $_SERVER['WINDIR'] != '' )
			{
				if ( defined('SERVER_CORPMYSQL_PATH') )
				{
					$mysql_path = SERVER_CORPMYSQL_PATH;
				}
				else
				{
					$mysql_path = dirname(dirname(dirname($parts['dirname']))).'/mysql/bin/mysql.exe';
				}

				if ( !file_exists($mysql_path) )
				{
					$this->replyError( text(1515).': '.text(1518).' - '.$mysql_path );
				}

				$result = shell_exec('call "'.$mysql_path.'" -?');
				if ( $result == '' )
				{
					$this->replyError( text(1515).': '.
					text(930).' - '.$mysql_path );
				}

				$mysql_command = 'call "'.$mysql_path.'" --host='.$hostname.' --user='.$username.
					' --password='.$password.' -e "source '.$sql_script.'" 2>&1';
			}
			else
			{
				$result = shell_exec( defined('MYSQL_HELP_COMMAND') ? MYSQL_HELP_COMMAND : 'mysql --help');
				if ( $result == '' )
				{
					$this->replyError( text(1515).': '.text(986) );
				}

				if ( defined('MYSQL_INSTALL_COMMAND') )
				{
					$mysql_command = str_replace('%1', $hostname,
					str_replace('%2', $username, str_replace('%3', $password,
					str_replace('%4', $sql_script, MYSQL_INSTALL_COMMAND) ) ) );
				}
				else
				{
					$mysql_command = 'mysql --host='.$hostname.' --user='.$username.
						' --password='.$password.' -e "source '.$sql_script.'"';
				}
			}
				
			$result = trim(preg_replace('/^Warning:.+$/mi', '', shell_exec( $mysql_command )), chr(10).chr(13));
			
			if ( $result != '' )
			{
				$this->replyError( text(1515).': '.$result.
					' - '.$mysql_command );
			}

			try {
                $this->connect($hostname, $username, $password, $dbname);
                // check the database structure
                if ( count(DAL::Instance()->QueryArray('select * from cms_SystemSettings')) < 1 ) {
                    $this->replyError(text(1517));
                }
			}
			catch( \Exception $e ) {
				$this->replyError( text(1516).': '.$e->getMessage() );
			}
		}

		// setup server settings
		$htdocs_dir = dirname($parts['dirname']);

		$settings_file_path = $htdocs_dir.'/settings_server.php';
		$f = fopen($settings_file_path, 'r', 1);

		if($f === false)
		{
			$this->replyError( text(1031).': '.$settings_file_path );
		}

		$file_content = fread($f, filesize($settings_file_path));
		fclose($f);

		$f = fopen($settings_file_path, 'w', 1);
		if($f === false)
		{
			$this->replyError( text(1031).': '.$settings_file_path );
		}

		$root_dir = dirname($htdocs_dir);
			
		$file_content = str_replace("?HOST", $hostname, $file_content);
		$file_content = str_replace("?USER", $username, $file_content);
		$file_content = str_replace("?PASS", $password, $file_content);
		$file_content = str_replace("?NAME", $dbname, $file_content);
		$file_content = str_replace("?ROOT", $root_dir, $file_content);

		fwrite($f, $file_content);
		fclose($f);

		mkdir($root_dir.'/files/');
		mkdir($root_dir.'/backup/');
		mkdir($root_dir.'/update/');

		// setup server constants
		$settings_file_path = $htdocs_dir.'/settings.php';

		$f = fopen($settings_file_path, 'r', 1);
		if( $f === false )
		{
			$this->replyError( text(1031).': '.$settings_file_path );
		}

		$file_content = fread($f, filesize($settings_file_path));
		fclose($f);

		$f = fopen($settings_file_path, 'w', 1);
		if( $f === false )
		{
			$this->replyError( text(1031).': '.$settings_file_path );
		}

		$file_content = str_replace("?CUUID", $this->gen_uuid(), $file_content);

		fwrite($f, $file_content);
		fclose($f);

        if ( function_exists('opcache_reset') ) opcache_reset();

		// report result of the operation
		$this->replyRedirect( '?' );
	}

	function gen_uuid()
	{
		list($usec, $sec) = explode(" ",microtime());
		return md5(strftime('%d.%m.%Y.%M.%H.%S').((float)$usec + (float)$sec).rand());
	}

	function connect($hostname, $username, $password, $dbname = "")
    {
        DAL::Destroy();
        $info = new MySQLConnectionInfo( $hostname, $dbname, $username, $password );

        if ( function_exists('mysqli_connect') ) {
            DALMySQLi::Instance()->Connect($info);
        }
        else {
            DALMySQL::Instance()->Connect($info);
        }
    }

    protected function getMySQLVersion() {
        return array_shift(DAL::Instance()->QueryArray('SELECT VERSION()'));
    }
}
