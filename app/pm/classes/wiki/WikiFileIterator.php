<?php

class WikiFileIterator extends OrderedIterator
{
 	function getFileLink() 
 	{
 		if ( $this->IsImage('Content')) 
 		{
			return '<a class="image_attach" href="'.$this->getFileUrl().'&.png" ' .
				'><img src="/images/image.png"> '.$this->getFileName('Content').'</a>';
 		}
 		else
 		{
			return '<a class="" href="'.$this->getFileUrl().'" ' .
				'><img src="/images/attach.png"> '.$this->getFileName('Content').'</a>';
 		}
 	}
 	
 	function getDisplayName() 
 	{
 		return $this->getFileLink().' ('.$this->getFileSizeKb('Content').' Kb)';
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
