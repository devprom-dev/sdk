<?php

class SystemTemplateIterator extends OrderedIterator
{
	function get( $attr )
	{
		switch( $attr )
		{
		    case 'Content':
		    	return $this->get_native($attr);
		    	
		    default:
		    	return parent::get($attr);
		}
	}
	
	function get_native($attr)
	{
		switch( $attr )
		{
		    case 'Content':
		    	$file_path = file_exists($this->getFilePath()) ? $this->getFilePath() : parent::get('BackupFileName'); 
		    	return file_get_contents($file_path);
		    	
		    default:
		    	return parent::get_native($attr);
		}
	}
	
	function getFilePath()
	{
		$language = strtolower(getSession()->getLanguageUid());
		return $this->object->getPath().$language.'/'.basename($this->get('BackupFileName'));
	}
}
