<?php

include_once "classes/widgets/ModuleBuilderPermissions.php";
include_once "classes/TransitionMetadataPermissionsBuilder.php";
include_once "classes/widgets/FunctionalAreaMenuPermissionsSettingsBuilder.php";
include_once "classes/TaskTypeMetadataPermissionsBuilder.php";

class permissionsPM extends PluginPMBase
{
	// returns builders which extend application behavior 
	public function getBuilders()
	{
		if ( !$this->getBasePlugin()->checkLicense() ) return array();
		return array(
				new TransitionMetadataPermissionsBuilder(),
				new TaskTypeMetadataPermissionsBuilder(),
				new PortfolioMyProjectsBuilder(),
				new ModuleBuilderPermissions(),
				new FunctionalAreaMenuPermissionsSettingsBuilder()
		);
	}
	
    function getModules()
 	{
		if ( !$this->getBasePlugin()->checkLicense() ) return array();
		return array (
 			'settings' => 
 				array(
 					'includes' => array( 'permissions/views/AccessRightPage.php' ),
 					'classname' => 'AccessRightPage',
 					'title' => text('permissions3'),
 					'description' => text(1817),
 					'AccessEntityReferenceName' => 'pm_AccessRight',
 					'AccessType' => ACCESS_MODIFY
 				),
 			'participants' => 
 				array(
 					'includes' => array( 'permissions/views/ParticipantPage.php' ),
 					'classname' => 'ParticipantPage',
 					'title' => text('permissions5'),
 					'description' => text(1815),
 					'AccessEntityReferenceName' => 'pm_Participant'
 				)
		);
 	}

    function getObjectActions( $object_it )
    {
    	$actions = array();

    	$can_set_permissions = $object_it->object instanceof ProjectPage 
    			&& $object_it->get('ParentPage') != ''
    			&& getFactory()->getAccessPolicy()->can_modify($object_it);
    	
    	if ( $can_set_permissions && $this->getBasePlugin()->checkLicense() )
    	{
    		if ( !is_object($this->module_permissions_it) ) {
    			$this->module_permissions_it = getFactory()->getObject('Module')->getExact('permissions/settings');
    		}
    		$actions[] = array( 
    		    'name' => translate('Права доступа'),
    			'url' => $this->module_permissions_it->get('Url').'?class='.
     			            get_class($object_it->object).'&id='.$object_it->getId()
    		);
    	}
    	
    	return $actions;
    }
    
    private $module_permissions_it = null;
}