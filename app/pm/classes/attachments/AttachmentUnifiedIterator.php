<?php
include_once "AttachmentIterator.php";

class AttachmentUnifiedIterator extends AttachmentIterator
{
	function getFileUrl() {
        return \EnvironmentSettings::getServerUrl() . '/file/'.$this->get('AttachmentClassName').'/'.$this->get('ProjectCodeName').'/'.$this->getId();
	}

	function getFilePath($attribute)
	{
		return SERVER_FILES_PATH.$this->get('AttachmentClassName').'/'.basename($this->get('FilePath'));
	}
}
