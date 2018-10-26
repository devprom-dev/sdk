<?php

include_once "procloud/classes/LicenseProcloud.php";

include "procloud/classes/LicenseRegistryBuilderProcloud.php";
include "procloud/classes/ArtefactProcloudModelBuilder.php";
include "procloud/classes/ArtefactTypeProcloudModelBuilder.php";
include "procloud/classes/UserProcloudModelBuilder.php";
include "procloud/classes/ProjectTemplateProcloudModelBuilder.php";
include "procloud/classes/MethodologyProcloudMetadataBuilder.php";

 ///////////////////////////////////////////////////////////////////////////////
 class ProcloudPlugin extends PluginBase
 {
 	function getNamespace()
 	{
 		return 'procloud';
 	}
 
  	function getFileName()
 	{
 		return basename(__FILE__);
 	}
 	
 	function getCaption()
 	{
 		return translate('Облако проектов');
 	}
 	
 	function getClasses()
 	{
 		return array (
 			'procloud.blogpost' => array ( 'ProCloudBlogPost', 'c_blog.php' ), 
 			'message' => array ( 'Message', 'c_message.php' ), 
 			'co_message' => array ( 'Message', 'c_message.php' ),
 		    'licensedata' => array ( 'LicenseData', 'LicenseData.php' )
 		);
 	}
 	
 	function getSectionPlugins()
 	{
 		return array( new ProCloudCoPlugin, new ProCloudAdminPlugin, new ProCloudPMPlugin );
 	}
 	
 	function getBuilders()
 	{
 		return array (
 				new LicenseRegistryBuilderProcloud()
 		);
 	}
 	
    function getAuthorizationFactories()
    {
        include_once dirname(__FILE__).'/procloud/classes/auth/AuthenticationDemoAnyUserFactory.php';
        	
        return array( new AuthenticationDemoAnyUserFactory() );
    }
    
  	function getIndex()
 	{
 	    return parent::getIndex() + 9998;
 	}
 }

 ///////////////////////////////////////////////////////////////////////////////
 class ProCloudCoPlugin extends PluginCoBase
 {
 	function getBuilders()
 	{
 		return array (
 				new ProjectTemplateProcloudModelBuilder()
 		);
 	}
 	
    function getModules()
    {
        return array(
            'demo' =>
                array(
                        'includes' => array( 'procloud/views/LaunchDemoProjectPage.php' ),
                        'classname' => 'LaunchDemoProjectPage'
                )
        );
    }
    
   	function getEntityAccess( $action, $role_ref_name, & $object )
 	{
 	   	if ( $object->getEntityRefName() == 'pm_Project' && $action == ACCESS_CREATE )
 	   	{
            return getFactory()->getObject('Project')->getRegistry()->Count(
                array (
                    new ProjectStatePredicate('active'),
                    new ProjectParticipatePredicate()
                )
            ) < 3;
 	   	}
 	}
    
    function getCommand( $name )
    {
        switch ( $name )
        {
            case 'launchdemoproject':
                return array(
                	'includes' => array( 'procloud/commands/c_launchdemoproject.php' )
                );
        }
    }
 }

 ///////////////////////////////////////////////////////////////////////////////
 class ProCloudAdminPlugin extends PluginAdminBase
 {
     function interceptMethodTableGetActions( & $table, & $actions )
     {
         if ( is_a($table, 'UserTable') )
         {
             if ( !class_exists('ExcelExportWebMethod', false) )
             {
                 include SERVER_ROOT_PATH.'core/methods/ExcelExportWebMethod.php';
             }
             
             $method = new ExcelExportWebMethod();
              
             if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
             
             array_push($actions, array(
                 'name' => $method->getCaption(),
                 'url' => $method->getJSCall()
             ));
         }
     }
      
      
 }
  
 //////////////////////////////////////////////////////////////////////////////
 class ProCloudPMPlugin extends PluginPMBase
 {
 	function getModules()
 	{
		return array (
 			'invite' => 
 				array(
 					'includes' => array( 'procloud/views/ProcloudParticipantPage.php' ),
 					'classname' => 'ProcloudParticipantPage',
 				    'AccessEntityReferenceName' => 'pm_Participant'
 					)
		        );
 	}
 	
    function getCommand( $name )
    {
        switch ( $name )
        {
            case 'finduser':
                return array(
                    'includes' => array( 'procloud/commands/c_finduser.php' )
                );

            case 'inviteuser':
                return array(
                    'includes' => array( 'procloud/commands/c_inviteuser.php' )
                );
        }
    }
 	
    function getBuilders()
    {
    	return array (
    			new ArtefactProcloudModelBuilder(),
    			new ArtefactTypeProcloudModelBuilder(),
    			new UserProcloudModelBuilder(),
    			new ProjectTemplateProcloudModelBuilder(),
    			new MethodologyProcloudMetadataBuilder()
    	);
    }

  	function getEntityAccess( $action, $role_ref_name, & $object )
 	{
 	   	if ( $object->getEntityRefName() == 'pm_Project' && $action == ACCESS_CREATE )
 	   	{
 	   		return getFactory()->getObject('Project')->getRegistry()->Count(
				array (
					new ProjectStatePredicate('active'),
					new ProjectParticipatePredicate()
				)
			) < 3;
 	   	}
 	}
    
  	function getHeaderMenus()
 	{
 		$project_it = getSession()->getProjectIt();
 		
 		if ( $project_it->get('Platform') != 'demo' ) return array();
 		
 		return array(
 				array (
 						'caption' => 'Начать работу в Devprom',
 						'class' => 'btn-success',
 						'url' => 'javascript: downloadproduct();'
 				),
 				array(
 						'class' => 'empty'
 				),
 				array(
 						'class' => 'empty'
 				),
 				array(
 						'class' => 'empty'
 				)
 		);
 	}
 	
  	function interceptMethodTableGetActions( & $table, & $actions )
 	{
 	    if ( is_a($table, 'ParticipantTable') )
 	    {
 	    	foreach( $actions as $key => $action )
 	    	{
 	    		if ( $action['uid'] == 'add-user' ) unset($actions[$key]);
 	    	}
 	    }
 	}
 	
  	function interceptMethodTableGetFilters( & $table, & $filters )
 	{
 	    if ( is_a($table, 'ParticipantTable') )
 	    {
 	        foreach( $filters as $key => $filter )
 	        {
 	             if ( $filter->getValueParm() == 'type' ) unset($filters[$key]);
 	        }
 	    }
 	}
 	
  	function interceptMethodListGetPredicates( & $list, & $predicates, $values )
 	{
 	 	if ( is_a($list, 'ParticipantList') )
 	    {
 	        foreach( $predicates as $key => $predicate )
 	        {
 	             if ( is_a($predicate, 'UserParticipanceTypePredicate') ) $predicates[$key] = new UserParticipanceTypePredicate('participant');
 	        }
 	    }
 	}
 	
 }
