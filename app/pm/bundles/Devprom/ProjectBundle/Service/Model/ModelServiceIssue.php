<?php

namespace Devprom\ProjectBundle\Service\Model;
use Devprom\ProjectBundle\Service\Model\ModelService; 

class ModelServiceIssue extends ModelService
{
	function set( $entity, $data, $id = '', $output = self::OUTPUT_TEXT )
	{
		if ( !is_numeric($data['Author']) && !is_array($data['Author']) ) {
			$data['CustomerEmail'] = $data['Author'];
			unset($data['Author']);
		}

		return parent::set( $entity, $data, $id, $output );
	}
}