<?php

class WikiFileIterator extends OrderedIterator
{
 	function getFileLink() 
 	{
        return '<a class="image_attach" data-fancybox="gallery" href="'.$this->getFileUrl().'&.png" ' . '>'.$this->getFileName('Content').'</a>';
 	}
 	
 	function getDisplayName() 
 	{
        $modified = getSession()->getLanguage()->getDateFormattedShort($this->get('RecordModified'));
        return $this->getFileLink().' ('.$modified.', '.$this->getFileSizeKb('Content').' '.translate('Kb').')';
 	}

    function getFileInfo()
    {
        $modified = getSession()->getLanguage()->getDateFormattedShort($this->get('RecordModified'));
        return $modified.', '.$this->getFileSizeKb('Content').' '.translate('Kb');
    }

	function getPageIt()
	{
		$page_it = $this->getRef('WikiPage');
		$type_it = getFactory()->getObject('WikiType')->getExact($page_it->get('ReferenceName'));
		if ( $type_it->getId() != '' ) {
			return getFactory()->getObject($type_it->get('ClassName'))->createCachedIterator($page_it->getRowset());
		}
		else {
			return $page_it;
		}
	}
}
