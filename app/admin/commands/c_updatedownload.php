<?php

include_once "MaintenanceCommand.php";

class UpdateDownload extends MaintenanceCommand
{
    var $data, $update_info;
    private $updateName = '';
    
	function validate()
	{
	    $this->data = JsonWrapper::decode(file_get_contents(DOCUMENT_ROOT.'conf/new-updates.json'));

	    if ( count($this->data) < 1 )
	    {
	        return false;
	    }
	    
	    foreach ( $this->data as $update_info )
	    {
	        if ( $update_info['version'] == $_REQUEST['parms'] )
	        {
	            $this->update_info = $update_info; 
	            
	            return true;
	        }
	    }
	    
	    return false;
	}

	function create()
	{
		global $model_factory, $_FILES;

		$logger = $this->getLogger();
		
		if ( is_object($logger) ) $logger->info('Download update on the url: '.$this->update_info['download_url']);
        
        set_time_limit(0);

        $info = parse_url($this->update_info['download_url']);
        
        $file_path = SERVER_UPDATE_PATH.basename($info['path']);
        
		if ( is_object($logger) ) $logger->error('Open the file: '.$file_path);
        
		$fp = fopen($file_path, 'w+');

        $curl = CurlBuilder::getCurl();
        curl_setopt($curl, CURLOPT_URL, $this->update_info['download_url'].'&iid='.INSTALLATION_UID);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 1200);
		curl_setopt($curl, CURLOPT_FILE, $fp);
		
		$result = curl_exec($curl);
		
		if ( $result === false )
		{
		    if ( is_object($logger) ) $logger->error( curl_error($curl) );

		    $error_text = curl_error($curl);
		    
		    curl_close($curl);

		    fclose($fp);
		    
		    $this->replyError( str_replace('%1', $error_text, text(1420)) );
		    
		    return;
		}
		
		curl_close($curl);
		
		fclose($fp);

		if ( is_object($logger) ) $logger->info('Update has been downloaded');
		
		$pathinfo = pathinfo($file_path);
		$this->updateName = $pathinfo['basename'];
		
		$this->replyRedirect( '?action=check&parms='.$pathinfo['basename'] );
	}

	function getUpdateName() {
	    return $this->updateName;
    }
}
