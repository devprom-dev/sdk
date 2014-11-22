<?php

include_once dirname(__FILE__)."/../SCMConnector.php";
include_once 'helpers/phpsvnclient.php';

class SubversionConnector extends SCMConnector
{
 	var $client;
 	
 	function init( $credentials )
 	{
 		parent::init( $credentials );

 		if ( $credentials->getUrl() != '' )
 		{
	 		$this->client = new phpsvnclient(
	 			$credentials->getUrl(), 
	 			$credentials->getLogin(), 
	 			$credentials->getPassword() 
	 		);
	
			$this->client->preferCurl();
			
			$this->log("Connecting to: ".$credentials->getUrl()." as ".$credentials->getLogin());
 		}
 		else
 		{
 			$this->log("Empty URL");
 		}
 	}
 	
 	function getDisplayName()
 	{
 		return 'Subversion';
 	}
 	
 	static function checkPrerequisites()
 	{
 		return '';
 	}
 	
 	function getCredentialsParmDescription( $parm )
 	{
		switch ( $parm )
		{
			case 'SVNPath':
				return text('sourcecontrol9');
				
			case 'RootPath':
				return text('sourcecontrol10');
		}
 	}
 	
	function getRecentLogs( $version = '', $limit = 20 )
	{
		ob_start();
		
		is_object($this->client) ? $this->client->setDebug() : '';
		
 		$current = is_object($this->client) ? $this->client->getVersion() : 0;
 		
 		$this->debug( ob_get_contents() );
 		
		ob_end_clean();
 		
		$this->log( $this->getLastError() );		
 		
		$this->log( "Current revision: ".$current );

 		if ( $version == '' )
 		{
	 		return $this->getRepositoryLog( max($current - $limit, 0), $current );
 		}
 		else
 		{
 			return $this->getRepositoryLog( 
 			    max(max($current - $limit, 0), $version), $current 
 			);
 		}
	}
 	
	function getFiles( $path )
	{
 		$registry = new SCMFileRegistry();
 		
 		if ( !is_object($this->client) ) return $registry->getAll();
 		
 		ob_start();
 		
		$this->client->setDebug();
 		
 		$files = $this->client->getDirectoryFiles($path);

 		$this->debug( ob_get_contents() );
 		
		ob_end_clean();
 		
		$this->log( $this->getLastError() );		
 		
		$this->log( "Found ".count($files)." files on the path ".$path );
 		
 		if ( $files !== false )
 		{
 			foreach( $this->parseFileNames($files, $path) as $info )
 			{
 				$registry->addFile(
 						$info['type'], $info['last-mod'], $info['path'], $info['status'], 
 						$info['name'], $info['length'], $info['creator'], $info['content-type']
				);
 			}
 		}
		
		return $registry->getAll();		
	}
	
	function getRepositoryLog( $from = 0, $to = -1 )
	{
 		$registry = new SCMCommitRegistry();
 		
 		$credentials = $this->getCredentials();

 		if ( !is_object($this->client) ) return $registry->getAll();
 		
 		ob_start();
 		
		$this->client->setDebug();
 		
 		$logs = $this->client->getRepositoryLogs( '/'.$credentials->getPath(), $from, $to );

 		$this->debug( ob_get_contents() );
 		
		ob_end_clean();
 		
		$this->log( $this->getLastError() );		
 		
		$this->log( "Found ".count($logs)." commits on the path ".('/'.$credentials->getPath()) );
 		
 		if ( $logs !== false )
 		{
 			while ( count($logs) > 0 && $logs[count($logs)-1]['version'] <= $from ) {
 				unset($logs[count($logs)-1]);
 			}
 			
	 		$logs = array_reverse($logs);
	 		
	 		foreach( $logs as $info )
	 		{
	 			$registry->addCommit(
	 					$info['version'], 
	 					$info['date'], 
	 					self::detectCharset($info['comment']),
	 					$info['author']
				);
	 		}
		}

		return $registry->getAll();
	}
	
	function getTextFile( $path, $version = '' )
	{
 		if ( !is_object($this->client) ) return '';
 		
 		ob_start();
 		
		$this->client->setDebug();
 		
		$content = $this->client->getFile( $path, $version == '' ? -1 : $version );

 		$this->debug( ob_get_contents() );
 		
		ob_end_clean();
		
		$this->log( $this->getLastError() );		
		
		return self::detectCharset($content);
	}
	
 	function getBinaryFile( $path, $version = '' )
	{
 		if ( !is_object($this->client) ) return '';
 		
 		ob_start();
 		
		$this->client->setDebug();
 		
		$data = $this->client->getFile( $path, $version == '' ? -1 : $version );

 		$this->debug( ob_get_contents() );
 		
		ob_end_clean();
		
		$this->log( $this->getLastError() );		
		
		return $data;
	}	
	
	function getFileLogs( $path, $initial_version = 0, $final_version = -1)
	{
		$registry = new SCMCommitRegistry();
		
 		if ( !is_object($this->client) ) return $registry->getAll();

 		ob_start();
 		
		$this->client->setDebug();
 		
 		$logs = $this->client->getFileLogs( IteratorBase::wintoutf8($path), $initial_version, $final_version );
 		
		$this->debug( ob_get_contents() );
 		
		ob_end_clean();
		 		
		$this->log( $this->getLastError() );		
 		
 		if ( $logs !== false )
 		{
	 		$logs = array_reverse($logs);
	 		
	 		foreach ( $logs as $info )
	 		{
	 			$registry->addCommit(
	 					$info['version'], 
	 					$info['date'], 
	 					self::detectCharset($info['comment']),
	 					$info['author']
				);
	 		}
 		}
		
		return $registry->getAll();
	}
	
	function getVersionFiles( $version )
	{
		$registry = new SCMFileActionRegistry();
		
 		$credentials = $this->getCredentials();
		
 		if ( !is_object($this->client) ) return $registry->getAll();
 		
 		ob_start();
 		
		$this->client->setDebug();
 		
 		$logs = $this->client->getRepositoryLogs('/'.$credentials->getPath(), $version, $version);

		$this->debug( ob_get_contents() );
 		
		ob_end_clean();
 		
		$this->log( $this->getLastError() );		
 		
		if ( $logs !== false )
		{
			$files = $logs[0]['files'];
			$output = array();
			
			for ( $i = 0; $i < count($files); $i++ )
			{
				if ( isset($logs[0]['add_files']) && in_array($files[$i], $logs[0]['add_files']) )
				{
					$action = translate('Добавлено');
				}
				
				if ( isset($logs[0]['mod_files']) && in_array($files[$i], $logs[0]['mod_files']) )
				{
					$action = translate('Изменено');
				}
	
				if ( isset($logs[0]['del_files']) && in_array($files[$i], $logs[0]['del_files']) )
				{
					$action = translate('Удалено');
				}
	
				array_push($output,
					array('type' => 'file',
						  'path' => $files[$i],
						  'name' => $files[$i],
						  'action' => $action,
						  ) );
			}
	
	 		// sort files in alphabetical order
			foreach ( $output as $key => $row) 
			{
			    $names[$key]  = $row['name'];
			    $types[$key]  = $row['type'];
			}
			
			array_multisort($types, SORT_ASC, $names, SORT_ASC, $output);
		}

		foreach( $output as $info )
		{
			$registry->addFileAction($info['type'], $info['path'], $info['name'], $info['action']);
		}
		
		return $registry->getAll();
	}
	
 	function transformUrl( $url, $path )
 	{
		if ( $path == '' )
		{
			$parts = preg_split("/svn\//i", $url);
			if ( count($parts) > 1 )
			{
				$folders = preg_split("/\//", $parts[1]);
				
				$url = $parts[0].'svn/'.$folders[0];
				array_shift($folders);
				
				$path = join($folders, "/");
			}
		}
 		
		return array( $url, $path );
 	}
	
 	function parseFileNames( $files, $directory )
	{
 		$files_amount = count($files);

 		for ( $i = 0; $i < $files_amount; $i++ )
 		{
 			if ( $files[$i]['path'] == trim($directory, '/') && $files[$i]['type'] == 'directory' )
 			{
 				unset($files[$i]);
 			}
 			else
 			{
 				$parts = preg_split('/\//', $files[$i]['path']);
 				$files[$i]['name'] = $parts[count($parts) - 1]; 
 				$files[$i]['path'] = $parts[count($parts) - 1]; 
 			}
 		}

		return $files;
	}	
	
	private function getLastError()
	{
		if ( !is_object($this->client) ) return '';
		
		return $this->client->getLastError();
	}
}
