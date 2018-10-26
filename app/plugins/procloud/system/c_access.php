<?php
/*
 * DEVPROM (http://www.devprom.net)
 * c_access.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class CoAccessPolicy extends AccessPolicy
 {
 	function getEntityAccess( $action_kind, $role_id, &$object ) 
 	{
 		global $model_factory, $project_it, $user_it, $session;
 		
		if( is_object($object) )
		{
			$ref_name = $object->getClassName();
		}

		$role_id = 0;
		
		switch ( $role_id ) 
		{
			case 0:
				switch ( $ref_name )
				{
					case 'co_Team':
					case 'co_TeamUser':
					case 'co_OutsourcingSuggestion':
					case 'co_Service':
					case 'co_ServiceRequest':
					case 'co_Message':
						return $action_kind == ACCESS_READ ||
							( isset($user_it) && $user_it->IsReal() );

					case 'pm_Methodology':
					case 'pm_Release':
					case 'cms_Language':
					case 'WikiPageFile':
					case 'Blog';
					case 'cms_SystemSettings':
					case 'pm_Vacancy':
					case 'co_ServiceCategory':
					case 'co_IssueOutsourcing':
					case 'co_Option':
					case 'BlogPostTag':
					case 'co_TeamState':
					case 'pm_BugetSettings':
					case 'co_TenderState':
					case 'co_TenderKind':
					case 'co_TenderParticipanceState':
					case 'pm_Poll':
						return $action_kind == ACCESS_READ;

					case 'WikiPageFile':
						if ( isset($project_it) )
						{
							return $action_kind == ACCESS_READ;
						}
						else
						{
							return $action_kind == ACCESS_READ;
						}
						
					case 'BlogPostFile':
						if ( isset($project_it) )
						{
							return $action_kind == ACCESS_READ &&
								$project_it->IsPublicBlog();
						}
						else
						{
							return $action_kind == ACCESS_READ;
						}

					case 'pm_ProjectUse':
					case 'pm_ProjectCreation':
					case 'pm_Artefact':
					case 'pm_ArtefactType':
					case 'co_ProjectSubscription':
					case 'co_SearchResult':
					case 'co_ProjectParticipant':
					case 'co_UserRole':
					case 'co_Tender':
					case 'co_TenderAttachment':
					case 'co_TenderParticipant':
					case 'cms_UserSettings':
					case 'cms_NotificationSubscription':
					case 'Tag':
					case 'WikiTag':
					case 'pm_Participant':
						return true;

					case 'Comment':
						return $action_kind != ACCESS_DELETE;

					case 'co_Bill':
						return $action_kind == ACCESS_READ;
					case 'co_BillOperation':
						return true;

					case 'co_Advise':
						return $action_kind != ACCESS_DELETE;

					case 'cms_User':
						return $action_kind != ACCESS_DELETE;

					case 'co_Rating':
					case 'co_RatingVoice':
					case 'co_OptionUser':
					case 'pm_DownloadAction':
					case 'pm_DownloadActor':
						return true;

					case 'co_Option':
						return $action_kind == ACCESS_READ;

					case 'pm_Question':
					case 'pm_ChangeRequest':
					case 'pm_Project':
					case 'pm_PublicInfo':
						return $action_kind != ACCESS_DELETE;
						
					default:
						return true;
				}
				break;

			default:
				return false;	
		}
	}
	
 	function getObjectAccess( $action_kind, $role_id, &$object_it ) 
 	{
       	global $model_factory, $project_it, $user_it;
		
		$ref_name = $object_it->object->getClassName();	
		
		$role_id = 0;

		switch ( $role_id ) 
		{
			case 0: 
				switch ( $ref_name )
				{
					case 'co_Team':
						return $object_it->get('Author') == $user_it->getId() || $action_kind == ACCESS_READ;

					case 'co_TeamUser':
						$team_it = $object_it->getRef('Team');
						return $team_it->get('Author') == $user_it->getId() || 
							$object_it->get('SystemUser') == $user_it->getId() || $action_kind == ACCESS_READ;

					case 'WikiPageFile':
					case 'Blog':
					case 'BlogPostFile':
					case 'cms_TempFile':
					case 'pm_Question':
					case 'co_TeamState':
						return $action_kind == ACCESS_READ;

					case 'pm_ProjectUse':
					case 'pm_ProjectTag':
					case 'WikiPage':
					case 'BlogPost':
					case 'co_ProjectSubscription':
					case 'co_SearchResult':
					case 'cms_UserSettings';
					case 'ObjectChangeLog';
					case 'pm_PollResult';
					case 'pm_PollItemResult';
						return true;

					case 'cms_User':
						return $action_kind == ACCESS_READ || $object_it->getId() == $user_it->getId();

					case 'co_Tender';
						return $action_kind == ACCESS_READ || 
							$object_it->get('SystemUser') == $user_it->getId() && !$object_it->hasProject();

					case 'co_TenderAttachment':
						$tender_it = $object_it->getRef('Tender');
						return $action_kind == ACCESS_READ || $tender_it->get('SystemUser') == $user_it->getId();

					case 'co_TenderParticipant':
						if ( $action_kind == ACCESS_READ )
						{
							return true;
						}
						else
						{
							$tender_it = $object_it->getRef('Tender');
							$team_it = $object_it->getRef('Team');
							
							return $tender_it->get('SystemUser') == $user_it->getId() ||
								$team_it->isTeamMember($user_it->getId());
						}

					case 'co_Service':
						return $object_it->get('Author') == $user_it->getId() || $action_kind == ACCESS_READ;
						
					case 'co_UserRole':
					case 'co_ProjectParticipant':
						return $object_it->get('SystemUser') == $user_it->getId() || $action_kind == ACCESS_READ;

					case 'co_ServiceRequest':
						return $object_it->get('Customer') == $user_it->getId() || 
							$object_it->IsOwner( $user_it->getId() ) || $action_kind == ACCESS_READ;
						
					case 'co_OutsourcingSuggestion':
						if ( $action_kind == ACCESS_MODIFY )
						{
							$issue_it = $object_it->getRef('IssueOutsourcing');
							$project_it = $issue_it->getRef('Project');
							
							return $project_it->IsUserParticipate($user_it->getId()) || 
								$object_it->get('SystemUser') == $user_it->getId();
						}
						
						if ( $action_kind == ACCESS_DELETE )
						{
							return $object_it->get('SystemUser') == $user_it->getId();
						}
						return false;

					case 'co_Advise':
					case 'pm_PublicInfo':
					case 'Comment':
						return $action_kind != ACCESS_DELETE;

					case 'co_Bill':
						return $action_kind == ACCESS_READ;
						
					case 'co_BillOperation':
						return true;

					case 'co_Rating':
					case 'co_RatingVoice':
					case 'pm_DownloadAction':
					case 'pm_DownloadActor':
					case 'pm_ProjectCreation':
					case 'pm_Artefact':
					case 'pm_ArtefactType':
						return true;

					case 'co_Option':
						return $action_kind == ACCESS_READ;
					case 'co_OptionUser':
						return $object_it->get('SystemUser') == $user_it->getId();

					case 'pm_ChangeRequest':
						return $action_kind == ACCESS_READ && $project_it->IsPublic();
						
					case 'co_Message':
						if ( $object_it->get('ToUser') > 0 )
						{
							return $object_it->get('Author') == $user_it->getId() ||
								$object_it->get('ToUser') == $user_it->getId();
						}
						else
						{
							$team_it = $object_it->getRef('ToTeam');

							return $object_it->get('Author') == $user_it->getId() ||
								$team_it->IsTeamMember($user_it->getId());
						}						

					case 'pm_Project':
						if ( is_object($user_it) && $user_it->count() > 0 )
						{
							return $object_it->HasUserAccess( $user_it->getId() );
						}
						else
						{
							return $action_kind == ACCESS_READ;
						}
				}
				break;

			default:
				return false;	
		}
	}
 }

?>