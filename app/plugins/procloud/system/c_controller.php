<?php
/*
 * DEVPROM (http://www.devprom.net)
 * c_controller.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */

 class CoController
 {
 	function url( $url )
 	{
 		return $url;
 	}
 	
 	function getDeleteUrl( $command, $object_id )
 	{
 		return '/co/command.php?class='.$command.'&action='.
 			CO_ACTION_DELETE.'&object_id='.$object_id;
 	}
 	
 	function getAllTeamsUrl()
 	{
 		return '/co/teams.php';
 	}
 	
 	function getTeamProfileUrl( $team_it )
 	{
 		return '/co/tm/'.$team_it->getSearchName();
 	}

 	function getTeamModifyUrl( $team_id )
 	{
 		return '/co/teams.php?mode=modifyteam&object_id='.$team_id;
 	}
 	
 	function getTeamUserSearchUrl( $team_id )
 	{
 		return '/co/teams.php?mode=teamuser&sub=search&object_id='.$team_id;
 	}

 	function getTeamUserAddUrl( $team_id, $user_id )
 	{
 		return '/co/teams.php?mode=teamuser&sub=add&object_id='.$team_id.'&user_id='.$user_id;
 	}

 	function getTeamUserSearchToInviteUrl( $team_id )
 	{
 		return '/co/teams.php?mode=teamuser&sub=searchtoinvite&object_id='.$team_id;
 	}

 	function getTeamUserInviteUrl( $team_id, $user_id )
 	{
 		return '/co/teams.php?mode=teamuser&sub=invite&object_id='.$team_id.'&user_id='.$user_id;
 	}

 	function getUserInviteUrl( $user_id )
 	{
 		return '/co/teams.php?mode=teamuser&sub=invite&user_id='.$user_id;
 	}

 	function getTeamInvitationConfirmUrl( $team_id, $user_id )
 	{
 		return '/co/command.php?class=teaminvitationconfirm&team_id='.$team_id.'&user_id='.$user_id.'&action=1';
 	}

 	function getTeamUserModifyUrl( $team_user_id )
 	{
 		return '/co/teams.php?mode=teamuser&sub=modify&object_id='.$team_user_id;
 	}

 	function getTeamUserDeleteUrl( $team_user_id )
 	{
 		return CoController::getDeleteUrl('teamusermanage', $team_user_id);
 	}

 	function getProfileUrl()
 	{
 		return '/co/profile.php';
 	}

 	function getProfileModifyUrl()
 	{
 		return '/co/profile.php?mode=modify';
 	}

 	function getProfileRoleModifyUrl( $role )
 	{
 		return '/co/profile.php?mode=role&role='.$role;
 	}

 	function getProjectProfileUrl( $codename )
 	{
 		return '/co/'.$codename.'/';
 	}
 	
 	function getProjectsUrl()
 	{
 		return '/co/projects.php';
 	}
 	
 	function getUserUrl( $user_it )
 	{
 		return '/co/us/'.$user_it->getSearchName();
 	}

	function getServicesUrl()
	{
 		return '/co/services.php';
	}
	
 	function getServiceAddUrl()
 	{
 		return '/co/services.php?mode=user&service=';
 	}

 	function getServiceAddByTeamUrl( $team_id )
 	{
 		return '/co/services.php?mode=user&team='.$team_id.'&service=';
 	}

 	function getServiceModifyUrl( $service_id )
 	{
 		return '/co/services.php?mode=user&service='.$service_id;
 	}

 	function getUserServicesUrl()
 	{
 		return '/co/services.php?mode=user';
 	}

 	function getServiceUrl( $service_it )
 	{
 		return '/co/sv/'.$service_it->getSearchName();
 	}

 	function getServiceCategoryUrl( $category_id )
 	{
 		return '/co/sv/cat/'.$category_id;
 	}

 	function getServiceAddRequestUrl( $service_id )
 	{
 		return '/co/services.php?mode=request&service='.$service_id;
 	}

 	function getServiceRequestCloseUrl( $request_id )
 	{
 		return '/co/command.php?class=servicerequestmanage&action='.
 			CO_ACTION_MODIFY.'&kind=close&object_id='.$request_id;
 	}

 	function getServiceRequestUrl( $request_id )
 	{
 		return '/co/services.php?mode=requests&request='.$request_id;
 	}
 	
 	function getVacanciesUrl()
 	{
 		return '/co/vacancies.php';
 	}

 	function getVacancyUrl( $vacancy_it )
 	{
 		return '/co/vacancies.php?mode=accept&vacancy='.$vacancy_it->getId();
 	}
 	
 	function getVacancyCommentUrl( $vacancy_it )
 	{
 		global $user_it;
 		
 		if ( $user_it->getId() < 1 )
 		{
 			return '/co/login.php?mode=more&page='.urlencode(CoController::getVacancyUrl($vacancy_it).'#comments');
 		}
 		else
 		{
 			return CoController::getVacancyUrl($vacancy_it).'#comments';
 		}
 	}

 	function getProjectUrl ( $codeName )
 	{
 		return _getServerUrl().'/co/'.$codeName.'/';
 	}

 	function getProductUrl ( $codeName )
 	{
		global $model_factory;

		$project = $model_factory->getObject('pm_Project');
		$project_it = $project->getByRef('CodeName', $codeName);

		if ( $codeName == 'devprom' ) return 'http://devprom.ru/';
		
 		if ( $project_it->get('DomainName') != '' )
 		{
 			return 'http://'.$project_it->get('DomainName').'/';
 		}
 		else
 		{
 			return _getServerUrl().'/site/'.$codeName.'/';
 		}
 	}

	function getCatalogueUrl()
	{
 		return '/co/catalogue.php';
	}

	function getMainBlogUrl()
	{
 		return '/co/blog.php';
	}
	
	function getVacanciesAtom()
	{
		return '/atom/Vacancy';
	}

	function getServicesAtom()
	{
		return '/atom/Service';
	}

	function getTeamsAtom()
	{
		return '/atom/Team';
	}

	function getProjectsAtom()
	{
		return '/atom/Project';
	}

	function getDevpromBlogAtom()
	{
		return '/atom/DBlog';
	}

	function getTasksAtom()
	{
		return '/atom/Outsourcing';
	}

	function getProjectBlogAtom( $project )
	{
		return _getServerUrl().'/atom/blog/'.$project;
	}
	
	function getAllIssuesUrl()
	{
		return '/co/outsourcing.php';
	}

	function getIssueUrl( $issue_it )
	{
		return '/co/outsourcing.php?issue='.$issue_it->getId();
	}

	function getIssueAcceptanceUrl( $issue_it )
	{
		global $user_it;
		
 		if ( $user_it->getId() < 1 )
 		{
 			return '/co/login.php?mode=more&page='.urlencode('/co/outsourcing.php?issue='.$issue_it->getId().'&mode=accept');
 		}
 		else
 		{
 			return '/co/outsourcing.php?issue='.$issue_it->getId().'&mode=accept';
 		}
	}

	function getIssueSuggestionUrl( $suggestion_it )
	{
		return '/co/outsourcing.php?mode=discuss&suggestion='.$suggestion_it->getId();
	}

	function getAdviseUrl( $advise_it )
	{
		return '/co/ad/'.$advise_it->getSearchName();
	}

	function getAdviseCommentUrl( $advise_id )
	{
		return '/co/ad/'.$advise_id.'#comments';
	}

	function getAdvisesUrl()
	{
		return '/co/advises.php';
	}

	function getAdvisesByThemeUrl( $theme_id )
	{
		return '/co/ad/theme/'.$theme_id;
	}

	function getAdviseApproveUrl( $advise_id )
	{
		return '/co/command.php?class=approveadvise&key='.md5(INSTALLATION_UID.$advise_id);
	}

 	function getVoteUrl( $object_it )
 	{
 		return '/co/command.php?class=ratingmanage&action=1&ObjectId='.$object_it->getId().
			'&ObjectClass='.$object_it->object->getClassName();
 	}

 	function getMessageUrl( $message )
 	{
 		return '/co/message/'.$message;
 	}

 	function getMessageToUserUrl( $user )
 	{
 		return '/co/profile.php?mode=message&to='.$user;
 	}

 	function getMessageToTeamUrl( $team )
 	{
 		return '/co/profile.php?mode=message&team='.$team;
 	}
 	
 	function getTendersUrl()
 	{
 		return '/co/tenders.php';
 	}

 	function getTenderUrl( $id )
 	{
 		return '/co/tenders.php?tender='.$id;
 	}

 	function getTenderModifyUrl( $id )
 	{
 		return '/co/tenders.php?mode=modify&tender='.$id;
 	}
 	
 	function getPostUrl( $project_it, $post_it )
 	{
 		$configuration = getConfiguration();
 		
 		if ( $configuration->hasTeams() )
 		{
 			if ( is_object($project_it) && $project_it->get('CodeName') != 'procloud' )
 			{
				if ( $project_it->HasProductSite() )
				{
					return $project_it->getViewUrl().'news/'.$post_it->getSearchName();
				}
				else
				{
	 				return '/co/'.$project_it->get('CodeName').'/blog/'.$post_it->getSearchName();
				}
 			}
 			else
 			{
 				return '/blog/'.$post_it->getSearchName();
 			}
 		}
 		else
 		{
 			return '/pm/'.$project_it->get('CodeName').'/'.$post_it->getViewUrl();
 		}
 	}
 	
 	function getPostTagUrl( $project_it, $tag_it )
 	{
		if ( is_object($project_it) && $project_it->get('CodeName') != 'procloud' )
		{
 			return '/co/'.$project_it->get('CodeName').'/blog/tag/'.$tag_it->getSearchName();
		}
		else
		{
			return '/blog/tag/'.$tag_it->getSearchName();
		}
 	}

 	function getProjectPageUrl( $project_it )
 	{
 		$configuration = getConfiguration();
 		
 		if ( $configuration->hasTeams() )
 		{
 			return '/co/'.$project_it->get('CodeName').'/';
 		}
 		else
 		{
 			return '/pm/'.$project_it->get('CodeName').'/';
 		}
 	}
 	
 	function getMovieLink( $name, $text )
 	{
 		return '<a href="/movie/'.$name.'">'.$text.'</a> <img style="margin-bottom:-3px;" border=0 src="/images/page_video.gif">';
 	}
 	
 	function getProjectCommentsUrl( $project_it, $object_it )
 	{
 		return '/pm/'.$project_it->get('CodeName').'/'.$object_it->getCommentsUrl();
 	}
 	
 	function getTemplates()
 	{
 		return array ('blogsmith', 'emporium', 'plainoffice', 'tricolor',
 			'vectorlover', 'techjunkie', 'envision', 'thunder');
 	}

 	function getGlobalUrl( $object_it )
 	{
 		global $project_it;
 		
 		if ( is_object($project_it) && $project_it->HasProductSite() )
 		{
 			return SitePageUrl::parse( $object_it );
 		}
 		else
 		{
 			return ParserPageUrl::parse( $object_it );
 		}
 	}
 }

 ///////////////////////////////////////////////////////////////////////////
 class CoEmailController extends CoController
 {
 	function parse( $url )
 	{
 		return _getServerUrl().$url;
 	}
 }

?>