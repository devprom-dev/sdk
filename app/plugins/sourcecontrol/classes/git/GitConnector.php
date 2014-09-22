<?php

include_once dirname(__FILE__)."/../SCMConnector.php";

use Gitter\Client;
use Gitter\Repository;
use Symfony\Component\Process\ExecutableFinder;

class GitConnector extends SCMConnector
{
 	private $repository = null;
 	
 	private function getRepositoryPath()
 	{
 		return SERVER_ROOT.'/git_repo/'.md5($this->getCredentials()->getUrl());
 	}
 	
 	function init( $credentials )
 	{
 		parent::init( $credentials );
 		
		try
		{
 			$client = new Gitter\Client($this->getToolPath());
 			
		 	$local_repository = $this->getRepositoryPath();
 			
			if ( !file_exists( $local_repository.'/config' ) ) 
	 		{
				mkdir( $local_repository, 0777, true );
				
				try
				{
					$this->repository = $client->createRepository($local_repository, true);
					
					$url = $credentials->getUrl();
					
					if ( $credentials->getLogin() != '' )
					{
						$url = preg_replace('/(https?:\/\/)/i', '$1'.$credentials->getLogin().':'.$credentials->getPassword().'@', $url);
					}
					
					$this->repository->setConfig("remote.origin.url", $url);
					$this->repository->setConfig("remote.origin.fetch", "+refs/heads/*:refs/remotes/origin/*");
					$this->repository->setConfig("branch.master.remote", "origin");
					$this->repository->setConfig("branch.master.merge", "refs/heads/master");

					$this->repository->setConfig("user.name", $credentials->getLogin());
					$this->repository->setConfig("user.email", $credentials->getLogin());
					
					$client->run($this->repository, "fetch");
					
					$this->log("Repository has been fetched successfully");
				}
				catch( Exception $e )
				{
					$this->log($e->getMessage());
				}
	 		}
	 		else
	 		{
				try
				{
		 			$this->repository = $client->getRepository($local_repository);
				}
				catch( Exception $e )
				{
					$this->log($e->getMessage());
				}
	 		}
		}
		catch( Exception $e )
		{
			$this->log($e->getMessage());
		}
 	}
 	
 	function resetCredentials()
 	{
 		$this->rrmdir($this->getRepositoryPath());
 	}
 	
	private function rrmdir($dir)
	{
	    foreach(glob($dir . '*') as $file)
	    {
	        if(is_dir($file))
	            $this->rrmdir($file.'/');
	        else
	            unlink($file);
	    }
	    rmdir($dir);
	}
 	
 	function getFiles( $path )
	{
 		$registry = new SCMFileRegistry();

 		try
 		{
 			if ( !is_object($this->repository) ) throw new Exception('Repository object has not been initialized');
 			
 			$path = array_pop(preg_split('/\//', $path));
 			
 			if ( $path == $this->getCredentials()->getPath() )
 			{
 				$path = $this->repository->getBranchTree("origin/".$this->getCredentials()->getPath());

 				$this->log("Branch tree has been used: ".$path);
 			}
 			
 			foreach ( $this->repository->getTree($path) as $key => $item )
 			{
 				if ( $item->IsTree() )
 				{
	 				$registry->addFile( 
	 						'directory', 
	 						'', 
	 						$item->getHash(), 
	 						'', 
	 						$item->getName(), 
	 						'', 
	 						'', 
	 						''
	 				);
 				}

 				if ( $item->IsBlob() )
 				{
	 				$registry->addFile( 
	 						'file', 
	 						'', 
	 						$item->getHash(), 
	 						'', 
	 						$item->getName(), 
	 						$item->getSize(), 
	 						'', 
	 						''
	 				);
 				}
 			}

			$this->log("Files have been retreived");
 		}
 		catch( Exception $e )
 		{
 			$this->log($e->getMessage());
 		}
 		
 		return $registry->getAll();
	}
	
	function getRecentLogs( $version = '', $limit = 10 )
	{
		try
 		{
 			if ( !is_object($this->repository) ) throw new Exception('Repository object has not been initialized');
 			
 			if ( $version != '' )
 			{
 				$commit = $this->repository->getCommit($version);

 				$this->log("Commit was found on version: ". $version);
 				
 				$since_date = $commit->getDate()->format('Y-m-d H:i:s');
 			}
 			
 			$this->log("Recent commits have been fetched since: ". $since_date);
 			
 			return $this->getRepositoryLog($since_date, $limit);
 		}
 		catch( Exception $e )
 		{
 			$this->log($e->getMessage());
 			
 			$registry = new SCMCommitRegistry();
 			
 			return $registry->getAll();
 		}
	}
	
	function getRepositoryLog( $from_date, $limit = 10 )
	{
 		$registry = new SCMCommitRegistry();

	 	try
 		{
 			if ( !is_object($this->repository) ) throw new Exception('Repository object has not been initialized');

 			$this->repository->getClient()->run($this->repository, "fetch");
 								
 			$options = '--all --max-count='.$limit;
 			
 			if ( $from_date != '' ) $options .= ' --since="'.$from_date.'"';
 			
 			$this->log("Fetch has been completed");
 			
 			$commits = $this->repository->getCommits($options);

 			$this->log("Commits have been fetched: ".count($commits));
 			
 			foreach( $commits as $commit )
 			{
 				$registry->addCommit( 
 						$commit->getShortHash(), 
 						$commit->getDate()->format('Y-m-d H:i:s'), 
 						$commit->getMessage(), 
 						$commit->getCommiter()->getName()
 				);
 			} 			
 		}
 		catch( Exception $e )
 		{
 			$this->log($e->getMessage());
 		}
 		
 		return $registry->getAll();
	}

	function getFileLogs( $path, $initial_version = 0, $final_version = -1)
	{
 		$registry = new SCMCommitRegistry();

	 	try
 		{
 			if ( !is_object($this->repository) ) throw new Exception('Repository object has not been initialized');

 			$path = array_pop(preg_split('/\//', $path));
 			
 			$commits = $this->repository->getCommits("--all -- ".$path);
 			
 			$this->log("File commits have been fetched: ".count($commits));
 			
 			foreach( $commits as $commit )
 			{
 				$registry->addCommit( 
 						$commit->getShortHash(), 
 						$commit->getDate()->format('Y-m-d H:i:s'), 
 						$commit->getMessage(), 
 						$commit->getCommiter()->getName()
 				);
 			} 			
 		}
 		catch( Exception $e )
 		{
 			$this->log($e->getMessage());
 		}
 		
 		return $registry->getAll();
	}
	
	function getVersionFiles( $version )
	{
 		$registry = new SCMFileActionRegistry();

 		try
 		{
 			if ( !is_object($this->repository) ) throw new Exception('Repository object has not been initialized');
 			
 			$commit = $this->repository->getCommit($version);
 			
 			$this->log("Commit has been fetched on version: ".$version);
 			
 			foreach ( $commit->getDiffs() as $key => $item )
 			{
 				if ( preg_match('/dev\/null/i', $item->getOld()) )
 				{
 					$action = translate('Добавлено');
 				}
 				else if ( preg_match('/dev\/null/i', $item->getNew()) )
 				{
 					$action = translate('Удалено');
 				}	
 				else
 				{
 					$action = translate('Изменено');
 				}
 				
 				$registry->addFileAction( 
 						'file', 
 						$item->getFile(), 
 						$item->getFile(), 
 						$action 
 				);
 			}
 		}
 		catch( Exception $e )
 		{
 			$this->log($e->getMessage());
 		}
 		
 		return $registry->getAll();
	}
	
	function getTextFile( $path, $version = '' )
	{
		return $this->getBinaryFile( $path, $version );
	}

	function getBinaryFile( $path, $version = '' )
	{
		try
 		{
 			if ( !is_object($this->repository) ) throw new Exception('Repository object has not been initialized');
 			
 			$path = array_pop(preg_split('/\//', $path));
 			
 			if ( $version != '' ) $path = $version.":".$path;
 			
 			$blog = $this->repository->getBlob($path);
 			
 			return $blog->output();
 		}
 		catch( Exception $e )
 		{
 			$this->log($e->getMessage());
 		}
	}
	
 	static function checkPrerequisites()
 	{
 		if ( !file_exists(static::getToolPath()) ) 
 		{
 			return str_replace('%1', static::getToolPath(), text('sourcecontrol39'));  
 		}
 		
 		return '';
 	}
	
 	function getDisplayName()
 	{
 		return 'Git';
 	}
 	
 	function getCredentialsParmDescription( $parm )
 	{
		switch ( $parm )
		{
			case 'SVNPath':
				return text('sourcecontrol11');
				
			case 'RootPath':
				return text('sourcecontrol12');
		}
 	}
 	
 	private static function getToolPath()
 	{
 		if ( static::checkWindows() )
 		{
 			return SERVER_ROOT.'/tools/git/bin/git.exe';
 		}
 		else
 		{
 			$finder = new ExecutableFinder();
            
 			return $finder->find('git', '/usr/bin/git');
 		}
 	}
}
 