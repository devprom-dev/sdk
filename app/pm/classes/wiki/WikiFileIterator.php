<?php

class WikiFileIterator extends OrderedIterator
{
 	function getFileLink() 
 	{
 		if ( $this->IsImage('Content')) 
 		{
			return '<a class="image_attach" href="'.$this->getFileUrl().'&.png" ' .
				'><img src="/images/image.png" style="margin-bottom:-4px;"> '.$this->getFileName('Content').'</a>'; 		
 		}
 		else
 		{
			return '<a class="" href="'.$this->getFileUrl().'" ' .
				'><img src="/images/attach.png" style="margin-bottom:-4px;"> '.$this->getFileName('Content').'</a>'; 		
 		}
 	}
 	
 	function getDisplayName() 
 	{
 		return $this->getFileLink().' ('.$this->getFileSizeKb('Content').' Kb)';
 	}
}
