<?php

include_once dirname(__FILE__)."/../SCMConnector.php";
include_once 'TFSClient.php';

define( 'SERVER_TFS_CLI_PATH', SERVER_ROOT.'/tools/tee-clc' );

class TFSConnector extends SCMConnector
{
    /**
     * @var TFSClient
     */
    var $client;

    function init($credentials)
    {
        parent::init($credentials);

        if ($credentials->getUrl() != '') {
            $this->client = new TFSClient(
                $credentials->getUrl(),
                $credentials->getLogin(),
                $credentials->getPassword(),
                SERVER_TFS_CLI_PATH,
                'devprom'
            );
        }
    }

    function getDisplayName()
    {
        return 'TFS';
    }
    
 	static function checkPrerequisites()
 	{
 		if ( !file_exists(SERVER_TFS_CLI_PATH) ) 
 		{
 			return str_replace('%1', SERVER_TFS_CLI_PATH, text('sourcecontrol37'));  
 		}
 		
 		if ( static::checkWindows() )
 		{
 			$cmd = 'java -version > NUL && echo yes || echo no';
 		}
 		else
 		{
 			$cmd = 'command -v java >/dev/null && echo "yes" || echo "no"';
 		}
 		
 		$result = exec($cmd);
 		
 		if ( strpos($result, 'no') !== false )
 		{
 			return text('sourcecontrol38');
 		}
 		
 		return '';
 	}

    function getCredentialsParmDescription($parm)
    {
        switch ($parm) {
            case 'SVNPath':
                return text('sourcecontrol26');

            case 'RootPath':
                return text('sourcecontrol27');
        }
    }

    function buildPath($path, $filename)
    {
        return $filename;
    }

    function getRecentLogs($version = '', $limit = 10) //todo: implement
    {
        return $this->getRepositoryLog();
    }

    function getFiles($path)
    {
        $registry = new SCMFileRegistry();
        
        if (!is_object($this->client)) return $registry->getAll();

        $files = $this->client->getDirectoryFiles($path);
        
        if ($files !== false)
        {
        	foreach( $files as $info )
        	{
        		$registry->addFile(
 						$info['type'], $info['last-mod'], $info['path'], $info['status'], 
 						$info['name'], $info['length'], $info['creator'], $info['content-type']
				);
        	}
        }

        return $registry->getAll();
    }

    function getRepositoryLog($from = 0, $to = -1) //todo: implement
    {
        $registry = new SCMCommitRegistry();

        $history = $this->client->getHistory("$/");

        foreach( $history as $info )
        {
        	$registry->addCommit($info['version'], $info['date'], $info['comment'], $info['author']);
        }
        
        return $registry->getAll();
    }

    function getTextFile($path, $version = '')
    {
        if (!is_object($this->client)) return '';

        $content = $this->client->getFile($path, $version == '' ? 'T' : $version);

        return $content;
    }

    function getBinaryFile($path, $version = '') //todo: implement
    {
        if (!is_object($this->client)) return '';

        return $this->client->getFile($path,
            $version == '' ? 'T' : $version);
    }

    function getFileLogs($path, $initial_version = 0, $final_version = -1) //todo: implement
    {
    	$registry = new SCMFileRegistry();
    	
        return $registry->getAll();
    }

    function getVersionFiles($version)
    {
        $registry = new SCMFileActionRegistry();

        $version = $this->client->getHistory("$/", $version, $version);

        $files = $version[0]['changes'];
        $sorted_files = array();
        
        foreach ($files as $file) 
        {
	        switch ($file['change-type']) {
	            case 'add' :
	                $action = translate('Добавлено');
	                break;
	            case 'edit' :
	                $action = translate('Изменено');
	                break;
	            case 'delete' :
	                $action = translate('Удалено');
	                break;
	            case 'rename' :
	                $action = translate('Добавлено (Переименование)');
	                break;
	            case 'delete, source rename' :
	                $action = translate('Удалено (Переименование)');
	                break;
	        }
	
	        array_push($sorted_files,
	            array('type' => 'file',
	                'path' => $file['server-item'],
	                'name' => $file['server-item'],
	                'action' => $action,
	            ));
	    }

        foreach( $sorted_files as $info )
        {
        	$registry->addFileAction($info['type'], $info['path'], $info['name'], $info['action']);
        }
        
		return $registry->getAll();
	}


    function mapIteratorDataToDbAttributes($iterator)
    {
        return array_merge(
            parent::mapIteratorDataToDbAttributes($iterator),
            array('VersionNum' => intval($iterator->get('Version')))
        );
    }

    function hasNumericVersion()
    {
        return true;
    }
}
