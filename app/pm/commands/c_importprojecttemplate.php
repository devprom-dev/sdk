<?php

use Devprom\ProjectBundle\Service\Project\ApplyTemplateService;

class ImportProjectTemplate extends CommandForm
{
 	function validate()
 	{
		global $_REQUEST, $model_factory;

		$this->object = $model_factory->
			getObject('pm_ProjectTemplate');

		$this->checkRequired( array('ProjectTemplate') );
			
		return true;
 	}
 	
 	function create()
	{
		global $_REQUEST, $project_it, $model_factory;
		
		$template_it = $this->object->getExact( 
			$_REQUEST['ProjectTemplate'] );
			
		if ( $template_it->count() > 0 )
		{
			$imported = array();
			
			$section = $model_factory->getObject('ProjectTemplateSections');
			
			$section_it = $section->getAll();
			
			$sections = $section_it->fieldToArray('ReferenceName');
			
			foreach ( $sections as $section )
			{ 
				if ( $_REQUEST[$section] == 'on' )
				{
					array_push($imported, $section);
				}
			}

			$service = new ApplyTemplateService();
			
			$service->apply($template_it, $project_it, $imported );
		}
		 
		$this->replyRedirect( '?area=stg', text(731) );
	}
}
