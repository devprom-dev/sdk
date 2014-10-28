<?php

class AttachmentIterator extends OrderedIterator
{
 	function getFileLink() 
 	{
 		if ( $this->IsImage('File')) 
 		{
			return '<a class="image_attach" id="File'.$this->getId().'" href="'.$this->getFileUrl().
				'&.png" title="'.$this->get('Description').'"><img src="/images/image.png"> '.$this->getFileName('File').'</a>'; 		
 		}
 		else
 		{
			return '<a class="" id="File'.$this->getId().'" href="'.$this->getFileUrl().
				'" title="'.$this->get('Description').'"><img src="/images/attach.png"> '.$this->getFileName('File').'</a>'; 		
 		}
 	}
 	
 	function getDisplayLink()
 	{
 		return $this->getFileLink().' ('.$this->getFileSizeKb('File').' Kb)';
 	}

 	function getDisplayName()
 	{
 		return $this->getDisplayLink();
 	}

	function getAnchorIt()
	{
	    $class_name = getFactory()->getClass($this->get('ObjectClass'));
	    
	    if ( !class_exists($class_name) ) return null;
	    
	    return getFactory()->getObject($class_name)->getExact($this->get('ObjectId'));
	}
}
