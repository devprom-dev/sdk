<?php

namespace Devprom\ProjectBundle\Service\Model;
use Devprom\ProjectBundle\Service\Model\ModelService; 

class ModelServiceBugReporting extends ModelService
{
	function set( $entity, $data, $id = '' )
	{
		$object = getFactory()->getObject($entity);
		
		// disable change log history and other data driven events
		$object->setNotificationEnabled(false);
		
		// normalize issue title before persist it
		$data['Caption'] = $this->normalizeText($data['Caption']);
		$data['TimesOccured'] = 1;
		
		return parent::set( $object, $data, $id );
	}
	
	protected function modify( $object_it, $data )
	{
		$data['TimesOccured'] = $object_it->get('TimesOccured') + 1;
		return parent::modify( $object_it, $data );
	}
	
	protected function normalizeText( $text )
	{
		return preg_replace('/\d+/i', 'X', $text);
	}
}