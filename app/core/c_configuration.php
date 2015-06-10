<?php
 
 class CommunityConfiguration
 {
 	function getCaption() {
 		return 'DEVPROM.Community';
 	}
 	
 	function getProjectCreationStrategy() 
 	{
 		return new ProjectActivationStrategy;
 	}
 	
 	function setPublicInfoAttributes( $parms, $access_level = null ) 
 	{
		$parms['IsParticipants'] = 'N';
		$parms['IsReleases'] = 'N';
		$parms['IsBlog'] = 'Y';
		$parms['IsKnowledgeBase'] = 'Y';
		$parms['IsChangeRequests'] = 'Y';
		$parms['IsPublicDocumentation'] = 'Y';
		$parms['IsPublicArtefacts'] = 'Y';
		
 		if ( $access_level == 'public' )
 		{
			$parms['IsProjectInfo'] = 'Y';
 		}
 		else
 		{
			$parms['IsProjectInfo'] = 'N';
 		}
		return $parms;
 	}
 	
 	function IsVariablePublicAccess()
 	{
 		return true;
 	}
 	
 	function IsAllUsersAvailable()
 	{
 		return false;
 	}

 	function getBackupAndRecoveryStrategy() 
 	{
 		global $_SERVER;
 		
 		if ( strpos($_SERVER['OS'], 'Windows') !== false || $_SERVER['WINDIR'] != ''  || $_SERVER['windir'] != '' )
 		{
	 		return new BackupAndRecoveryOnWindows;
 		}
 		else
 		{
	 		return new BackupAndRecoveryStrategy;
 		}
 	}
 	
 	function getDiskVolumeCapacity( $used_by_artefacts ) 
 	{
 		return array(20 * 1024, $used_by_artefacts, 'Kb');
 	}
 	
 	function exceedMaxArtefactsVolume( $used_by_artefacts ) 
 	{
 		global $project_it;
 		
 		if ( is_object($project_it) && $project_it->get('CodeName') == 'devprom' )
 		{
 			return false;
 		}
 		else
 		{
 			return $used_by_artefacts > 20 * 1024;
 		}
 	}
 	
 	function hasCounters() {
 		return true;
 	}

 	function hasLinksHolders() {
 		return true;
 	}

 	function hasLinkOnIssueFormPublication() {
 		return true;
 	}
 	
 	function hasProjectExport() {
 		return true;
 	}

 	function hasBookShelf() {
 		return true;
 	}

 	function hasUsers() {
 		return true;
 	}
 	
 	function hasTeams() {
 		return true;
 	}

 	function hasCommonBlog() {
 		return true;
 	}

	
 	function getUpdateRootDirectory() {
 		return 'community';
 	}
 	
 	function ParticipantHasOwnCredentials() {
 		return true;
 	}
 	
 	function CanSelectAnyUserForParticipance() {
 		return false;
 	}
 	
 	function CanViewTasksInOtherProjects() {
 		return false;
 	}
 	
 	function CanViewNewsChannels() {
 		return true;
 	}
 	
 	function DisplayUsersVisits() {
 		return true;
 	}
 	
 	function CanUserRegister()
 	{
 		return true;
 	}
 	
 	function VacanciesUsed()
 	{
 		return true;
 	}
 	
 	function CanOutsourceIssue()
 	{
 		return true;
 	}
 	
 	function getMaxEmailRecipients()
 	{
 		return 20;
 	}
 	
 	function useEmailQueue()
 	{
 		return false;
 	}
 	
 	function IsHttpsPreferCurl()
 	{
 		return false;
 	}
 	
 	function UseEmailNotificationsOnBlog()
 	{
 		return false;
 	}
 	
 	function getCookiesDomain()
 	{
 		global $_SERVER;
 		return $_SERVER['HTTP_HOST'];
 	}
 	
 	function getKBCaption()
 	{
 		return translate('Описание');
 	}
 	
 	function IsFeedbackAuthRequired()
 	{
 		return true;
 	}

 	function HasProjectSubscriptions()
 	{
 		return true;
 	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////
 class CorporateConfiguration extends CommunityConfiguration
 {
 	function getCaption() {
 		return 'DEVPROM.Corporate';
 	}
 	
 	function getProjectCreationStrategy() 
 	{
 		return new ImmediateCreationStrategy;
 	}
 	
 	function setPublicInfoAttributes( $parms ) {
		$parms['IsProjectInfo'] = 'Y';
		$parms['IsBlog'] = 'Y';
		$parms['IsParticipants'] = 'Y';
		$parms['IsReleases'] = 'Y';
		$parms['IsKnowledgeBase'] = 'Y';
		$parms['IsChangeRequests'] = 'Y';
		return $parms;
 	}
 
 	function IsVariablePublicAccess()
 	{
 		return false;
 	}
 
  	function IsAllUsersAvailable()
 	{
 		return true;
 	}
 
 	function getDiskVolumeCapacity( $used_by_artefacts ) 
 	{
 		if ( !is_dir(SERVER_FILES_PATH) )
 		{
 			mkdir( SERVER_FILES_PATH );
 			
	 		if ( !is_dir(SERVER_FILES_PATH) )
	 		{
	 			return 0;
	 		}
 		}
 		
 		return array(round(disk_free_space( SERVER_FILES_PATH ) / 1024 / 1024 / 1024, 1), 
 			round($used_by_artefacts / 1024 / 1204, 1), 'Gb');
 	}
 	
 	function exceedMaxArtefactsVolume( $used_by_artefacts ) {
 		return false;
 	}

 	function hasCounters() {
 		return false;
 	}

 	function hasLinksHolders() {
 		return false;
 	}
 	
 	function hasLinkOnIssueFormPublication() {
 		return false;
 	}

 	function hasProjectExport() {
 		return false;
 	}
 	
 	function hasBookShelf() {
 		return false;
 	}

 	function hasTeams() {
 		return false;
 	}

 	function hasCommonBlog() {
 		return false;
 	}

 	function getUpdateRootDirectory() {
 		return 'corporate';
 	}

 	function ParticipantHasOwnCredentials() {
 		return false;
 	}
 	
 	function CanSelectAnyUserForParticipance() {
 		return true;
 	}
 	
 	function CanViewTasksInOtherProjects() {
 		return true;
 	}
 	
  	function CanViewNewsChannels() {
 		return false;
 	}

 	function DisplayUsersVisits() {
 		return true;
 	}

 	function CanUserRegister()
 	{
 		return false;
 	}
 	
 	function VacanciesUsed()
 	{
 		return false;
 	}

 	function CanOutsourceIssue()
 	{
 		return false;
 	}

 	function getMaxEmailRecipients()
 	{
 		return 0;
 	}

 	function useEmailQueue()
 	{
 		return true;
 	}

 	function IsHttpsPreferCurl()
 	{
 		return true;
 	}

 	function UseEmailNotificationsOnBlog()
 	{
 		return true;
 	}

 	function getCookiesDomain()
 	{
 		return '';
 	}

 	function getKBCaption()
 	{
 		return translate('База знаний');
 	}

 	function IsFeedbackAuthRequired()
 	{
 		return false;
 	}
 	
 	function HasProjectSubscriptions()
 	{
 		return false;
 	}
 }

 //////////////////////////////////////////////////////////////////////////////////////
 class OutsourcingConfiguration extends CorporateConfiguration
 {
 	function getCaption() {
 		return 'DEVPROM.Outsourcing';
 	}

 	function useEmailQueue()
 	{
 		return false;
 	}

 	function IsHttpsPreferCurl()
 	{
 		return false;
 	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////
 class SiteConfiguration extends CorporateConfiguration
 {
 	function getProjectCreationStrategy() 
 	{
 		return new ImmediateCreationStrategy;
 	}

 	function getUpdateRootDirectory() {
 		return 'site';
 	}

 	function hasUsers() {
 		return false;
 	}

 	function ParticipantHasOwnCredentials() {
 		return true;
 	}
 	
 	function CanSelectAnyUserForParticipance() {
 		return true;
 	}

 	function IsHttpsPreferCurl()
 	{
 		return false;
 	}
 }
 
?>