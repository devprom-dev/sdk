<?php

include_once "ExceptionHandlerListener.php";

class ExceptionHandlerListenerDevprom extends ExceptionHandlerListener
{
	private $transport = 'tcp';
	private $path = '/api/v1.0/errors';
	private $host = '';
	private $port = '';
	
	public function handle( $data, $code )
	{
		$url_parts = parse_url(DEVOPSSRV);
		$this->host = $url_parts['host'];
		$this->port = $url_parts['port'];
		try
		{
			$this->post( 
					array (
							'caption' => $data['error']['message'],
							'stacktrace' => nl2br(var_export($data, true)),
							'host' => $data['server']['SERVER_NAME'],
							'address' => $data['server']['SERVER_ADDR'],
							'version' => $_SERVER['APP_VERSION'],
							'source' => 'Devprom Application',
							'project' => INSTALLATION_UID
					)
			);
		}
		catch(Exception $e)
		{
			error_log($e->getMessage());
			error_log('Unhandled exception: '.print_r($data, true));
		}
	}

	public function captureException( $e )
	{
	}

    private function post($data_to_send)
    {
		$headers = 0;
      	$remote = $this->transport . '://' . $this->host . ':' . $this->port;

      	if ( strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' )
      	{
			$cmd = "curl -X POST -H 'Content-Type: application/json' ";
        	$cmd .= " -d '" . JsonWrapper::encode($data_to_send) . "' "
           		 . " 'http://".$this->user.":".$this->password."@".$this->host.':'.$this->port.$this->path."' > /dev/null 2>&1 &";

        	exec($cmd, $output, $exit);
        	
        	return $exit;
      	}
      	else
      	{
	      	$context = stream_context_create();
      		$fp = stream_socket_client($remote, $err, $errstr, 10, STREAM_CLIENT_CONNECT, $context);
    	    if ($fp)
        	{
        		$data_to_send = JsonWrapper::encode($data_to_send);
        		
	          	$req = '';
    	      	$req .= "POST $this->path HTTP/1.1\r\n";
        	  	$req .= "Host: ".$this->host."\r\n";
				$req .= "Content-type: application/json\r\n";    	      	
        	  	$req .= 'Content-length: ' . strlen($data_to_send) . "\r\n";
        	  	$req .= "Connection: close\r\n\r\n";
        	  	$req .= $data_to_send;

        	  	fwrite($fp, $req);
    	      	
	          	$response = "";
	          	
    	      	if (true || $this->debugSending)
        	  	{
            		while(!preg_match("/^HTTP\/[\d\.]* (\d{3})/", $response))
	            	{
    	          		$response .= fgets($fp, 128);
        	    	}

            		fclose($fp);

            		return $response;
          		}
          		else
          		{
            		fclose($fp);

            		return 0;
          		}
        	}
        	else
        	{
          		throw new Exception('Unable send report');
        	}
      	}
    }
}