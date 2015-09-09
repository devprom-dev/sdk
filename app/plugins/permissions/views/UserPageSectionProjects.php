<?php
include_once SERVER_ROOT_PATH."core/views/PageInfoSection.php";

class UserProjectsSection extends InfoSection
{
	var $user_it;

	function UserProjectsSection( $user_it )
	{
		$this->user_it = $user_it;
		
		parent::InfoSection();
	}

	function getCaption() 
	{
		return translate('Проекты');
	}

	function getRenderParms()
	{
	    return array_merge( parent::getRenderParms(), array(
	        'user_it' => $this->user_it,
	        'project_it' => getFactory()->getObject('pm_Project')->getRegistry()->Query(
									array (
											new ProjectParticipatePredicate($this->user_it->getId())
									)
							)
	    ));
	}
	
	function getTemplate()
	{
	    return 'permissions/views/templates/UserPageSectionProjects.php';
	}
}
