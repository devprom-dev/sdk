<?php

class ProjectTemplateIterator extends OrderedIterator
{
 	function getDisplayName()
 	{
 		return $this->getRef('Language')->get('CodeName').': '.parent::getDisplayName();
 	}
}