<?php

class ArtefactIterator extends OrderedIterator
{ 
 	function moveToArchive()
 	{
 		$this->object->modify_parms($this->getId(),
 			array('IsArchived' => "Y"));
 	}

 	function extractFromArchive()
 	{
 		$this->object->modify_parms($this->getId(),
 			array('IsArchived' => "N"));
 	}
 	
	function getUidUrl()
	{
		return $this->getFileUrl();
	}

	function getDownloadsAmount()
	{
		return getFactory()->getObject('pm_DownloadAction')->getDownloads($this);
	}
	
	function getVersion()
	{
		return $this->get('Version'); 
	}
	
	function IsAuthorizedDownload()
	{
		return $this->get('IsAuthorizedDownload') == 'Y';
	}
}