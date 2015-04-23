<?php

namespace Devprom\ProjectBundle\Service\Model;
use Devprom\ProjectBundle\Service\Model\ModelService; 

class ModelServiceBugReporting extends ModelService
{
	function set( $entity, $data, $id = '' )
	{
		$url_parts = parse_url(DEVOPSSRV);
		
		$json = array (
				'caption' => $data['Caption'],
				'stacktrace' => $data['Description'],
				'host' => $data['ServerName'],
				'address' => $data['ServerAddress'],
				'version' => $data['SubmittedVersion'],
				'source' => 'Devprom Backend',
				'project' => 'bfced568ec8da2faef45338ff1839d80'
		);
		
		$cmd = "curl -X POST -H 'Content-Type: application/json' ";
       	$cmd .= " -d '" . \JsonWrapper::encode($json) . "' "
       		 . " 'http://".$url_parts['host'].':'.$url_parts['port'].'/1/errors'."' > /dev/null 2>&1 &";

       	exec($cmd, $output, $exit);
	}
}