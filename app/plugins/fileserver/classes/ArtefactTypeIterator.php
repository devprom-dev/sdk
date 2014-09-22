<?php

class ArtefactTypeIterator extends OrderedIterator
{
	function getDisplayName() 
	{
		global $project_it;
		
		$uid = new ObjectUID;
		$codename = $uid->getProject($this);

 		if ( $project_it->get('CodeName') != $codename )
 		{
			$prefix = '{'.$codename.'} ';
 	 	}

		return $prefix.parent::getDisplayName();
	}

 	function getArtefactsCount()
 	{
 		global $model_factory;
 		
 		$artefact = $model_factory->getObject('pm_Artefact');
 		
 		return $artefact->getByRefArrayCount(
 			array( 'Kind' => $this->getId() ) );
 	}
} 
