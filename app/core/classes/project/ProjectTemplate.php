<?php

include "ProjectTemplateIterator.php";
include "sorts/SortProjectTemplateLanguageClause.php";
include "predicates/ProjectTemplateExceptEditionPredicate.php";
include "ProjectTemplateRegistryTeam.php";

class ProjectTemplate extends Metaobject
{
 	function __construct() 
 	{
 		global $model_factory;
 		
 		$user_it = getSession()->getUserIt();
 		
 		parent::__construct('pm_ProjectTemplate', new ProjectTemplateRegistryTeam($this) );

 		$this->setAttributeDefault( 'ProductEdition', 'custom' );
 		
 		$sorts = array();
 		
 		if ( is_object($user_it) )
 		{
 			if ( $user_it->get('Language') < 1 )
 			{
				$settings = $model_factory->getObject('cms_SystemSettings');
				$settings_it = $settings->getAll();
				
				$lang = $settings_it->get('Language');
 			}
 			else
 			{
				$lang = $user_it->get('Language');
 			}
 			
 			$sorts[] = new SortProjectTemplateLanguageClause($lang);
 		}
 		else
 		{
 			$sorts[] = new SortAttributeClause('Language');
 		}

		$sorts[] = new SortAttributeClause('OrderNum');
		
		$this->setSortDefault($sorts);
 	}
 	
 	function createIterator() 
 	{
 		return new ProjectTemplateIterator( $this );
 	}

	function getObjects( $section_name )
	{
		global $model_factory;

		$sectionSet = $model_factory->getObject('ProjectTemplateSections');
		$section_it = $sectionSet->getAll();
		
		$section_it->moveTo( 'ReferenceName', $section_name );
		
		return $section_it->get('items');
	}
	
	function getTemplatePath( $file_name )
	{
		$dirname = SERVER_ROOT_PATH.'templates/project';
		if ( !is_dir($dirname) )
		{
 			mkdir ( $dirname ); 
		}
 		
 		return $dirname.'/'.$file_name;
	}
	
 	function dropTemplate( $file_name, $exported )
 	{
 	    global $model_factory;
 	    
 		$file = fopen ( $this->getTemplatePath($file_name), 'w+' );
 		
	 	fwrite( $file, '<?xml version="1.0" encoding="utf-8"?>' );
	 	fwrite( $file, '<entities>' );

 		foreach ( $exported as $entity )
 		{
			$objects = $this->getObjects( $entity );
	
			foreach( $objects as $object )
			{
			    // export records related to the hosted project only
			    $object->addFilter( new FilterBaseVpdPredicate() );
			    
			    // get all records and serialize it into xml subtree
		 		fwrite( $file, $object->serialize2Xml() );
			}
 		}
 		
	 	fwrite( $file, '</entities>' );
 		fclose ( $file );
 	}
 	
 	function isAttributeVisible( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'IsDefault':
 				return false;
 				
 			default:
 				return parent::IsAttributeVisible( $attr );
 		}
 	}
}