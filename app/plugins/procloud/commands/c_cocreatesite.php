<?php
/*
 * DEVPROM (http://www.devprom.net)
 * c_advisemanage.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */
 require_once(dirname(__FILE__).'/c_cocreateproject.php');
 
 class CoCreateSite extends CommandForm
 {
 	function validate()
 	{
		global $_REQUEST, $model_factory, $user_it;

		// proceeds with validation
		if ( $user_it->getId() < 1 )
		{
			$this->checkRequired( 
				array('Login', 'Email', 'Password', 'PasswordRepeat') );

			// check a password
			if ( $_REQUEST['Password'] != $_REQUEST['PasswordRepeat'] )
			{
				$this->replyError( 
					$this->getResultDescription( -11 ) );
			}

			// check a login
			if ( strpos($_REQUEST['Login'], '@') !== false )
			{
				$this->replyError( 
					$this->getResultDescription( -12 ) );
			}

			// check for license agreement
			if ( !in_array('Conditions', array_keys($_REQUEST)) ) 
			{
				$this->replyError( 
					$this->getResultDescription( -13 ) );
			}
		}
		
		$this->checkRequired( 
			array('CodeName', 'Project', 'Question', 'Template', 'QuestionHash') );
		
		// check for answer
		$question = $model_factory->getObject('cms_CheckQuestion');
		
		$check_result = $question->checkAnswer( $_REQUEST['QuestionHash'],
			$this->Utf8ToWin($_REQUEST['Question']) );
		
		if ( !$check_result )
		{
			$this->replyError( 
				$this->getResultDescription( -14 ) );
		}
		
		return true;
 	}
 	
 	function create()
	{
		global $_REQUEST, $model_factory, $user_it;

		$model_factory->enableVpd(false);
		$model_factory->object_factory->access_policy = new AccessPolicy;

		$user = $model_factory->getObject('cms_User');
		$project = $model_factory->getObject('pm_Project');
		
		// check a user is unique
		if ( $user_it->getId() < 1 )
		{
			$this->checkUnique( $user, 'Login' );
			$this->checkUnique( $user, 'Email' );

			$_REQUEST['Login'] = $this->Utf8ToWin($_REQUEST['Login']);
			$_REQUEST['Password'] = $this->Utf8ToWin($_REQUEST['Password']);
			
			$parms['Caption'] = $_REQUEST['Login'];
			$parms['Login'] = $_REQUEST['Login'];
			$parms['Email'] = $_REQUEST['Email'];
			$parms['Password'] = $_REQUEST['Password'];
			$parms['Language'] = $_REQUEST['Language'];
			
			$user_id = $user->add_parms( $parms );
			
			$this->user_it = $user->getExact($user_id);
			
			$session = getSession();
			$session->open( $this->user_it );
		}
		else
		{
			$this->user_it = $user_it;
		}
		
		// check a project is unique
		$this->checkUnique( $project, 'CodeName' );

		if ( !$project->validCodeName($_REQUEST['CodeName']) ) 
		{
			$this->replyError( 
				$this->getResultDescription( -15 ) );
		}

		$this->configuration = getConfiguration();
		$this->creation_strategy = new ProductSiteStrategy();

		$_REQUEST['Caption'] = $this->Utf8ToWin($_REQUEST['Project']);
		$_REQUEST['Codename'] = $_REQUEST['CodeName'];
		$_REQUEST['Language'] = $_REQUEST['Language'] == 2 ? 'EN' : 'RU';
		$_REQUEST['Access'] = $_REQUEST['Template'];
		$_REQUEST['Template'] = 'issuetr_ru.xml';
		$_REQUEST['User'] = $this->user_it->getId();

		// создаем проект
		$result = $this->creation_strategy->execute();

		if ( $result < 1 )
		{
			$this->replyError($this->getResultDescription( $result )); 
		}
		else
		{
			$this->replySuccess($this->getResultDescription( $result )); 
		}
	}
 
 	function getResultDescription( $result )
	{
		switch($result)
		{
			case -1:
				return text('procloud200');
				
			case -2:
				return text('procloud201');
				
			case -3:
				return text('procloud202');
				
			case -4:
				return text('procloud203');
				
			case -5:
				return text('procloud204');
				
			case -6:
				return text('procloud205');
				
			case -7:
				return text('procloud206');
				
			case -8:
				return text('procloud207');
				
			case -9:
				return text('procloud208');
				
			case -10:
				return text('procloud209');
				
			case -11:
				return text('procloud211');
				
			case -12:
				return text('procloud212');

			case -13:
				return text('procloud215');

			case -14:
				return text('procloud216');
				
			case -15:
				return text('procloud208');

			default:
				if ( $result > 10 )
				{
					return text('procloud642');
				}
				else
				{
					return parent::getResultDescription( $result );
				}
		}
	}
 }
 
 ////////////////////////////////////////////////////////////////////////////
 class ProductSiteStrategy extends ProjectActivationStrategy
 {
 	function createProject()
 	{
 		global $model_factory, $project_it, $methodology_it;
 		
 		$project_id = parent::createProject();
 		
 		if ( $project_id > 0 )
 		{
 			$uid = new ObjectUID;

 			$project = $model_factory->getObject('pm_Project');
 			$project_it = $project->getExact( $project_id );
 			
 			$project_it->modify( 
 				array( 'Tools' => $this->access ) );

 			$public = $model_factory->getObject('pm_PublicInfo');
 			$public_it = $public->getByRef('Project', $project_it->getId());

			$public_it->modify(
				array ( 'IsProjectInfo' => 'N', 
						'IsPublicDocumentation' => 'Y',
						'IsPublicArtefacts' => 'Y',
						'IsBlog' => 'Y' ) 
				);

			// turn off the introduction helper window
			$part_it = $project_it->getLeadIt();
			$settings = new ActivateUserSettings( $part_it->getId() );
			
	 		$settings->setSettingsValue('ShowIntroduction', 'N');

			// get projects knowledge base
 			$knowledge = $model_factory->getObject('ProjectPage');
 			$knowledge_it = $knowledge->getExact( $project_it->getKnowledgeBaseId() );

 			// root page for site sections
 			$main_id = $knowledge->add_parms( 
 				array ('Caption' => translate('Страницы сайта'), 
 					   'ParentPage' => $knowledge_it->getId(),
 					   'ReferenceName' => 'KnowledgeBase',
 					   'Project' => $project_it->getId() )
 				);

 			$temp_id = $knowledge->add_parms( 
 				array ('Caption' => translate('Главная'),
 					   'ParentPage' => $main_id,
 					   'ReferenceName' => 'KnowledgeBase',
 					   'Project' => $project_it->getId() )
 				);

			$this->tagSitePage($temp_id);
			$this->tagPage($temp_id, 'main');

			$temp_it = $knowledge->getExact($temp_id);
 			$temp_it->modify(
 				array ('Content' => str_replace('%2', '/room/'.$project_it->get('CodeName').'/action/publish',
 					str_replace('%1', $uid->getGotoUrl($temp_it), text('procloud461') ) ) )
 				);

			// news page			
 			$temp_id = $knowledge->add_parms( 
 				array ('Caption' => translate('Новости'), 
 					   'ParentPage' => $main_id,
 					   'ReferenceName' => 'KnowledgeBase',
 					   'Project' => $project_it->getId() )
 				);


			$this->tagSitePage($temp_id);
			$this->tagPage($temp_id, 'news');

			// features description (product page)
 			$temp_id = $knowledge->add_parms( 
 				array ('Caption' => translate('Продукт'), 
 					   'Content' => text('procloud465'),
 					   'ParentPage' => $main_id,
 					   'ReferenceName' => 'KnowledgeBase',
 					   'Project' => $project_it->getId() )
 				);

			$this->tagSitePage($temp_id);
			$this->tagPage($temp_id, 'product');

			$temp_it = $knowledge->getExact( $temp_id );
			$knowledge->modify_parms( $temp_it->getId(),
				array ('Content' => str_replace('%1', $uid->getGotoUrl($temp_it), text('procloud465') ) ) );

			// download page			
 			$temp_id = $knowledge->add_parms( 
 				array ('Caption' => translate('Загрузить'), 
 					   'ParentPage' => $main_id,
 					   'ReferenceName' => 'KnowledgeBase',
 					   'Project' => $project_it->getId() )
 				);

			$this->tagSitePage($temp_id);
			$this->tagPage($temp_id, 'download');

			$temp_it = $knowledge->getExact($temp_id);
			
			$text = str_replace('%1', '/pm/'.$project_it->get('CodeName').'/artefacts.php', text('procloud469') );
			$text = str_replace('%2', '/room/'.$project_it->get('CodeName').'/action/publish', $text );
			$text = str_replace('%3', $uid->getGotoUrl($temp_it), $text );
			
 			$temp_it->modify( array ('Content' => $text ) );

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

			// news
 			$blog = $model_factory->getObject('Blog');
 			$blog_it = $blog->getExact($project_it->getBlogId());
 			
 			$post = $model_factory->getObject('BlogPost');
 			
 			$temp_id = $post->add_parms( 
 				array( 'Blog' => $project_it->getBlogId(),
 					   'Caption' => text('procloud462'),
 					   'Content' => text('procloud463'),
 					   'AuthorId' => $part_it->getId() )
 				);

			$temp_it = $post->getExact( $temp_id );
			
			$post->modify_parms( $temp_it->getId(),
				array ('Content' => str_replace('%1', '/pm/'.$project_it->get('CodeName').'/index.php?blog_id='.$blog_it->getId(),
					str_replace('%2', $uid->getGotoUrl($temp_it), text('procloud463') ) ) ) );
				
			// documentation
 			$help = $model_factory->getObject('HelpPage');
 			
 			$temp_id = $help->add_parms(
 				array ( 'Caption' => text('procloud466') )
 			);
			$temp_it = $help->getExact( $temp_id );
			
			$temp_it->modify( array(
				'Content' => str_replace('%1', $uid->getGotoUrl($temp_it), text('procloud467') ) 
			));
 		}
 		
 		return $project_id;
 	}
 
  	function tagSitePage( $wiki_id )
 	{
 		global $model_factory, $page_tag;
 		
 		if ( !isset($page_tag) )
 		{
 			$tag = $model_factory->getObject('Tag');
 			$page_tag = $tag->add_parms(array('Caption' => 'sitepage'));
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
 }
  
?>