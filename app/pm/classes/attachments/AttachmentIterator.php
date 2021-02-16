<?php

class AttachmentIterator extends OrderedIterator
{
 	function getFileLink() 
 	{
 		if ( $this->IsImage('File')) 
 		{
			return '<a class="image_attach" data-fancybox="gallery" id="File'.$this->getId().'" href="'.$this->getFileUrl().
				'" title="'.$this->get('Description').'">'.$this->getFileName('File').'</a>';
 		}
 		else
 		{
			return '<a id="File'.$this->getId().'" target="_blank" href="'.$this->getFileUrl().
				'" title="'.$this->get('Description').'">'.$this->getFileName('File').'</a>';
 		}
 	}
 	
 	function getDisplayLink()
 	{
        $modified = $this->getDateFormattedShort('RecordModified');
 		return $this->getFileLink().' ('.$modified.', '.$this->getFileSizeKb('File').' '.translate('Kb').')';
 	}

    function getFileInfo()
    {
        $modified = $this->getDateFormattedShort('RecordModified');
        return $modified.', '.$this->getFileSizeKb('File').' '.translate('Kb');
    }

 	function getDisplayName()
 	{
 		return $this->getDisplayLink();
 	}

 	function getDisplayNameSearch( $prefix = '' )
    {
        return $this->getFileName('File');
    }

	function getAnchorIt()
	{
	    $class_name = getFactory()->getClass($this->get('ObjectClass'));
	    if ( !class_exists($class_name) ) return $this->object->getEmptyIterator();
	    return getFactory()->getObject($class_name)->getExact($this->get('ObjectId'));
	}
}
