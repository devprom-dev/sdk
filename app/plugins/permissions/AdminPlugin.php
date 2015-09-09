<?php

include_once "views/UserPageSectionProjects.php";

class permissionsAdmin extends PluginAdminBase
{
	// returns builders which extend application behavior 
	public function getBuilders()
	{
		if ( !$this->getBasePlugin()->checkLicense() ) return array();
		return array(
				new PortfolioMyProjectsBuilder()
		);
	}

    function getObjectActions( $object_it )
    {
    	$actions = array();

    	if ( $object_it->object instanceof User )
    	{
    		if ( !is_object($this->method_add_participant) ) {
    		 	$this->method_add_participant = new UserRelateToProjectWebMethod($object_it);
    		}
			if ( $this->method_add_participant->hasAccess() && $this->getBasePlugin()->checkLicense() ) {
				$this->method_add_participant->setUser($object_it);
				$actions[] = array( 
			        'name' => $this->method_add_participant->getCaption(),
					'url' => $this->method_add_participant->getJSCall( array('user' => $object_it->getId()) ) 
			    );
    		}
    	}
    	
    	return $actions;
    }
    
 	function getPageInfoSections( $page )
 	{
 		$sections = array();
 		
 		if ( $page instanceof UserPage && $this->getBasePlugin()->checkLicense() ) {
	 		$object_it = $page->getObjectIt();
	 		if ( $page->needDisplayForm() && is_object($object_it) && $object_it->getId() > 0 ) {
	 				$sections[] = new UserProjectsSection($object_it);  
	 		}
 		}
 		
 		return $sections;
 	}
    
 	private $method_add_participant = null;
}