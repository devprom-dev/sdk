<?php

class ProjectRoleIterator extends OrderedIterator
{
 	function getViewUrl()
 	{
 	    $session = getSession();
 	    
 		if ( $this->get('VPD') != '' )
 		{
 			return $session->getApplicationUrl().'participants/list?role='.$this->getId();
 		}
 		else
 		{
 			return parent::getViewUrl();
 		}
 	}
}
