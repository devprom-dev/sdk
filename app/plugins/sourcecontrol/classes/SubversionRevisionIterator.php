<?php

class SubversionRevisionIterator extends OrderedIterator
{
 	function get( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'Caption':
 			    
 			    if ( $this->get('Version') == '' ) return '';
 			    
 			    $repo_it = $this->getRef('Repository');
 			    
 				return $repo_it->getDisplayName().':'.$this->get('Version').' ('.$this->get('CommitDate').')';
 			
 			default:
 			    
 				return parent::get( $attr );
 		}
 	}
 	
 	function getViewUrl()
 	{
 		global $project_it;
 		
 		return '/pm/'.$project_it->get('CodeName').
 			'/module/sourcecontrol/revision?mode=details&version='.$this->get('Version').
 				'&subversion='.$this->get('Repository');
 	}
}
