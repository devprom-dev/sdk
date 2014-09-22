<?php
 
 class StoreProjectTemplate extends CommandForm
 {
 	function validate()
 	{
		global $_REQUEST, $model_factory;

		$this->object = $model_factory->
			getObject('pm_ProjectTemplate');

		$this->checkRequired( 
			array('Caption', 'FileName') );
			
		$valid_file_name = preg_match ("/^[a-zA-Z0-9][a-zA-Z0-9\-\_\.]+[a-zA-Z0-9]?$/i", 
			$_REQUEST['FileName'] );
		
		if ( !$valid_file_name )
		{
			$this->replyError( text(726) );
		}
		
		return true;
 	}
 	
 	function create()
	{
		global $_REQUEST, $model_factory;
		
		$exported = array();

		$section = $model_factory->getObject('ProjectTemplateSections');
		
		$section_it = $section->getAll();
		
		$sections = $section_it->fieldToArray('ReferenceName');
		
		foreach ( $sections as $section )
		{ 
			if ( $_REQUEST[$section] == 'on' )
			{
				array_push($exported, $section);
			}
		}
		
		$this->object->dropTemplate( $_REQUEST['FileName'], $exported );

		$template_it = $this->object->getByRefArray(
			array( 'FileName' => $_REQUEST['FileName'] )
			);
			
		if ( $template_it->count() < 1 )
		{
			$this->object->add_parms(
				array ( 'Caption' => $this->object->utf8towin($_REQUEST['Caption']),
						'Description' => $this->object->utf8towin($_REQUEST['Description']),
						'FileName' => $this->object->utf8towin($_REQUEST['FileName']), 
						'Language' => $_REQUEST['Language'] )
				);
		}
		
		$this->replySuccess( text(724) );
	}
 }
 
?>