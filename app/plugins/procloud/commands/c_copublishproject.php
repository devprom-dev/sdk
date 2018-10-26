<?php
/*
 * DEVPROM (http://www.devprom.net)
 * c_copublishproject.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */
 
 class CoPublishProject extends CommandForm
 {
 	function validate()
 	{
		global $_REQUEST, $model_factory, $user_it, $project_it;

		// proceeds with validation
		if ( !is_object($user_it) || !is_object($project_it) )
		{
			return false;
		}
		
		if ( !$project_it->HasUserAccess($user_it->getId()) )
		{
			return false;
		}

		if ( $project_it->IsPublic() && !array_key_exists('IsPublic', $_REQUEST) )
		{
			return true;
		}

		$this->checkRequired( array('Description', 'Tags') );
		$this->checkWordsCount( 'Description', 2 );
		
		return true;
 	}
 	
 	function modify( $id )
	{
		global $_REQUEST, $model_factory, $project_it, $user_it;

 		$model_factory->object_factory->access_policy = new AccessPolicy;

		if ( $_REQUEST['Description'] != '' )
		{
			$project_it->object->removeNotificator( 'EmailNotificator' );
			$project_it->modify ( 
				array( 'Description' => $project_it->utf8towin($_REQUEST['Description']) ) );
		}

		$post_it = $project_it->getPostIt();
		
		if ( $post_it->count() > 0 && $_REQUEST['News'] != '' )
		{
			$post_it->object->removeNotificator( 'EmailNotificator' );
			$post_it->modify ( 
				array( 'Content' => $post_it->utf8towin($_REQUEST['News']) ) );
		}

		$kb_it = $project_it->getProductPageIt();
		
		if ( $kb_it->count() > 0 && $_REQUEST['FullDescription'] != '' )
		{
			$kb_it->modify ( 
				array( 'Content' => $kb_it->utf8towin($_REQUEST['FullDescription']) ), false );
		}

		$tag_it = $project_it->getTagsIt();
		
		if ( $_REQUEST['Tags'] != '' )
		{
			while ( !$tag_it->end() )
			{
				$tag_it->object->removeNotificator( 'EmailNotificator' );
				$tag_it->object->delete($tag_it->getId());
				$tag_it->moveNext();
			}
			
			$items = preg_split('/,/', $project_it->utf8towin($_REQUEST['Tags']));

			foreach ( $items as $item )
			{
				if ( trim($item) != '' )
				{
					$tag_it->object->removeNotificator( 'EmailNotificator' );
					$tag_it->object->add_parms(
						array('Project' => $project_it->getId(), 'Caption' => trim($item)) );
				}
			}
		}

		$info = $model_factory->getObject('pm_PublicInfo');
		$info->removeNotificator( 'EmailNotificator' );
		
		$info_it = $info->getByRef( 'Project', $project_it->getId() );
		
		$publishedfirsttime = $info_it->get('IsProjectInfo') == 'N';
		
		if ( $info_it->count() > 0 )
		{
			$parms = array( 'IsProjectInfo' => $publishedfirsttime || array_key_exists('IsPublic', $_REQUEST) ? 'Y' : 'N' );
			
			$parms['IsBlog'] = array_key_exists('IsBlog', $_REQUEST) ? 'Y' : 'N';
			$parms['IsParticipants'] = array_key_exists('IsParticipants', $_REQUEST) ? 'Y' : 'N';
			$parms['IsKnowledgeBase'] = 'Y';
			$parms['IsPublicArtefacts'] = array_key_exists('IsArtefacts', $_REQUEST) ? 'Y' : 'N';
			$parms['IsPublicDocumentation'] = array_key_exists('IsPublicDocumentation', $_REQUEST) ? 'Y' : 'N';
			
			$info_it->modify ( $parms );
		}

		$this->publishSite();

		if ( $publishedfirsttime )
		{
			$change_log = new Metaobject('ObjectChangeLog');
			$lead_it = $project_it->getLeadIt();
			
			$parms['Caption'] = translate('Проект опубликован');
			$parms['ObjectId'] = $project_it->getId();
			$parms['EntityRefName'] = $project_it->object->getClassName();
			$parms['EntityName'] = $project_it->object->getDisplayName();
			$parms['ChangeKind'] = 'modified';
			$parms['Author'] = $lead_it->getId();
			$parms['Content'] = $parms['Caption'];
			$parms['VisibilityLevel'] = 1;
			$parms['SystemUser'] = $user_it->getId();
	
			$change_log->add_parms($parms);
	
			// send admin notification on published project
			$settings = $model_factory->getObject('cms_SystemSettings');
	 		$settings_it = $settings->getAll();
	
			$project = $model_factory->getObject('pm_Project');
			$cloud_it = $project->getByRef('CodeName', 'procloud');
			
			if ( $cloud_it->count() > 0 )
			{
				$lead_it = $cloud_it->getLeadIt();
				
		   		$mail = new HtmlMailbox;
		   		
		   		while ( !$lead_it->end() )
		   		{
		   			$mail->appendAddress($lead_it->get('Email'));
		   			$lead_it->moveNext();
		   		}
		   		
		   		$mail->setBody(str_replace('%1', _getServerUrl().'/main/'.$project_it->get('CodeName'), text('procloud597')));
		   		$mail->setSubject( text('procloud598') );
		   		$mail->setFrom($settings_it->getHtmlDecoded('AdminEmail'));
				$mail->send();
			}
		}

		$this->replySuccess( 
			$this->getResultDescription( 1000 ) );
	}

	function publishSite()
	{
		global $_REQUEST, $model_factory, $project_it, $user_it;

		$use_custom_design = array_key_exists('IsCustomDesign', $_REQUEST);
		$template_defined = $_REQUEST['Template'] != '';
		
		if ( !$use_custom_design && !$template_defined )
		{
			return;
		}
		
		if ( $_REQUEST['Template'] == 'common' )
		{
			$project_it->object->removeNotificator( 'EmailNotificator' );
		    $project_it->modify( array( 'Tools' => '' ) );
	
			$creation = $model_factory->getObject('pm_ProjectCreation');
			$creation->removeNotificator( 'EmailNotificator' );

			$creation_it = $creation->getByRef( 'Project', $project_it->getId() );
	
			if ( $creation_it->count() > 0 )
			{
				$creation_it->modify( 
					array('Methodology' => '4' ) );
			}
			
			return;
		}

		$artefact = $model_factory->getObject('pm_Artefact');
		$artefact->removeNotificator( 'EmailNotificator' );

		$artefact_it = $artefact->getByRef('Caption', 'style.css');
		
		// user wants a template, remove site design elements were prepared earlier
		if ( $template_defined && $artefact_it->count() > 0 )
		{
			$type_id = $artefact_it->get('Kind');
			
			$artefact_it = $artefact->getByRef('Kind', $type_id);
			while ( !$artefact_it->end() )
			{
				$artefact->delete($artefact_it->getId());
				$artefact_it->moveNext();
			}

			$artefact_type = $model_factory->getObject('pm_ArtefactType');
			$artefact_type->removeNotificator( 'EmailNotificator' );
			
			$artefact_type->delete($type_id);
		}

		// user wants custom design and it wasnt created before
		$artefact_it = $artefact->getByRef('Caption', 'style.css');
		
		if ( $use_custom_design && $artefact_it->count() < 1 )
		{ 
			// prepare site design elements
			$artefact_type = $model_factory->getObject('pm_ArtefactType');
			$artefact_type->removeNotificator( 'EmailNotificator' );
			
			$type_id = $artefact_type->add_parms( 
				array( 'Caption' => translate('Дизайн сайта продукта'),
					   'IsDisplayedOnSite' => 'N' )
				);
				
			if ( $type_id < 1 )
			{
				return false;
			}
			
			// define list of files		
			$style_dir = SERVER_ROOT_PATH.'procloud/templates/css/'.$_REQUEST['Template'];
			$files = array( 'style.css' );
			
		    $handler = opendir($style_dir.'/images');
		
		    while ( $file = readdir($handler) ) 
		    {
		        if ($file != '.' && $file != '..')
		            $files[] = 'images/'.$file;
		    }
		
		    closedir($handler);
		    
			// preserve original style elements
			$tmp_dir = $style_dir.'/u'.$user_it->getId();
	
			mkdir( $tmp_dir );
			mkdir( $tmp_dir.'/images' );
		    
		    foreach ( $files as $file )
		    {
				copy ( $style_dir.'/'.$file, $tmp_dir.'/'.$file );
		    }
	
		    // append new artefacts
		    foreach ( $files as $file )
		    {
		    	$this->appendFile( $tmp_dir.'/'.$file, $type_id );
		    }
		    
		    rmdir($tmp_dir.'/images');
		    rmdir($tmp_dir);			
		}

		// store site templage		
		if ( $use_custom_design )
		{
			$template = 'custom';
		}
		else
		{
			$template = $_REQUEST['Template'];
		}
		
		$project_it->object->removeNotificator( 'EmailNotificator' );
	    $project_it->modify( 
	    	array( 'Tools' => $template ) );

		$creation = $model_factory->getObject('pm_ProjectCreation');
		$creation->removeNotificator( 'EmailNotificator' );
			
		$creation_it = $creation->getByRef( 'Project', $project_it->getId() );

		if ( $creation_it->count() > 0 )
		{
			$creation_it->modify( 
				array('Methodology' => ( $template != '' ) ? '5' : '4' ) );
		}
		
		// append additional pages if required
		$page_it = $project_it->getSitePageIt( 'main' );
		
		if ( $page_it->count() < 1 )
		{
			$knowledge = $model_factory->getObject('ProjectPage');
			$knowledge->removeNotificator( 'EmailNotificator' );
			
			$kb_it = $project_it->getProductPageIt();
			$main_id = $kb_it->get('ParentPage'); 

			// main page			
			$temp_id = $knowledge->add_parms( 
				array ('Caption' => translate('Главная'),
					   'ParentPage' => $main_id,
					   'ReferenceName' => 'KnowledgeBase',
					   'Project' => $project_it->getId(),
					   'OrderNum' => 10 )
				);
	
			$this->tagSitePage($temp_id);
			$this->tagPage($temp_id, 'main');
			
			// news page			
			$temp_id = $knowledge->add_parms( 
				array ('Caption' => translate('Новости'), 
					   'ParentPage' => $main_id,
					   'ReferenceName' => 'KnowledgeBase',
					   'Project' => $project_it->getId() ) 
				);
	
			$this->tagSitePage($temp_id);
			$this->tagPage($temp_id, 'news');
	
			// download page			
			$temp_id = $knowledge->add_parms( 
				array ('Caption' => translate('Загрузить'), 
					   'ParentPage' => $main_id,
					   'ReferenceName' => 'KnowledgeBase',
					   'Project' => $project_it->getId() ) 
				);
	
			$this->tagSitePage($temp_id);
			$this->tagPage($temp_id, 'download');
	
			// documentation page			
			$temp_id = $knowledge->add_parms( 
				array ('Caption' => translate('Документация'), 
					   'ParentPage' => $main_id,
					   'ReferenceName' => 'KnowledgeBase',
					   'Project' => $project_it->getId() ) 
				);
	
			$this->tagSitePage($temp_id);
			$this->tagPage($temp_id, 'docs');
	
			// support page			
			$temp_id = $knowledge->add_parms( 
				array ('Caption' => translate('Поддержка'), 
					   'ParentPage' => $main_id,
					   'ReferenceName' => 'KnowledgeBase',
					   'Project' => $project_it->getId() ) 
				);
	
			$this->tagSitePage($temp_id);
			$this->tagPage($temp_id, 'support');
	
			// contacts page			
			$temp_id = $knowledge->add_parms( 
				array ('Caption' => translate('Контакты'), 
					   'ParentPage' => $main_id,
					   'ReferenceName' => 'KnowledgeBase',
					   'Project' => $project_it->getId() ) 
				);
	
			$this->tagSitePage($temp_id);
			$this->tagPage($temp_id, 'team');
		}		
	}
	
	function tagSitePage( $wiki_id )
	{
		global $model_factory, $page_tag, $project_it;
		
		if ( !isset($page_tag) )
		{
			$tag = $model_factory->getObject('Tag');
			$tag_it = $tag->getByRefArray(
				array( 'Caption' => 'sitepage' )
				);
				
			$page_tag = $tag_it->getId();
		}
		
		$tag = $model_factory->getObject('WikiTag');
		$tag->add_parms( array('Tag' => $page_tag, 'Wiki' => $wiki_id) );
	}
	
	function tagPage( $wiki_id, $caption )
	{
		global $model_factory;
	
		$tag = $model_factory->getObject('Tag');
		$tag_id = $tag->add_parms( array('Caption' => $caption) );
		
		$tag = $model_factory->getObject('WikiTag');
		$tag->add_parms( array('Tag' => $tag_id, 'Wiki' => $wiki_id) );
	}
	
	function appendFile( $source_path, $type_id )
	{
		global $model_factory, $_FILES;
		
		$_FILES['Content']['tmp_name'] = $source_path;
		$_FILES['Content']['name'] = basename($source_path);

		$artefact = $model_factory->getObject('pm_Artefact');
		$artefact->add_parms(
			array ( 'Caption' => basename($source_path),
					'Kind' => $type_id ) 
			);
	}

	function getResultDescription( $result )
	{
		global $project_it;
		
		switch($result)
		{
			case 3:
				return text('procloud595');

			case 900:
				return text('procloud596');

			case 1000:
				if ( $project_it->HasProductSite() )
				{
					return str_replace('%1', CoController::getProductUrl($project_it->get('CodeName')), text('procloud618'));
				}
				else
				{
					return str_replace('%1', _getServerUrl().'/main/'.$project_it->get('CodeName'), text('procloud594'));
				}

			default:
				return parent::getResultDescription( $result );
		}
	}
 }
 
?>