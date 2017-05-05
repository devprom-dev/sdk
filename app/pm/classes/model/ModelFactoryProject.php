<?php

include "classes.php";

class ModelFactoryProject extends ModelFactoryExtended
{
	protected function buildClasses()
	{
		return array_merge( parent::buildClasses(), array(
			'pm_calendarinterval' => array('Calendar'),
			'wikipage' => array( 'PMWikiPage'),
			'wikipagetype' => array( 'WikiTypeBase'),
			'blogpost' => array( 'PMBlogPost'),
			'pm_tasktype' => array( 'TaskType'),
			'pm_release' => array( 'Iteration'),
		    'pm_task' => array( 'Task'),
			'pm_changerequest' => array( 'Request'),
			'pm_participant' => array( 'Participant'),
			'pm_function' => array( 'Feature'),
			'pm_methodology' => array( 'Methodology' ),
		    'pm_attachment' => array( 'Attachment'),
			'pm_activity' => array( 'Activity'),
			'pm_requesttag' => array( 'RequestTag'),
			'pm_question' => array( 'Question' ),
		    'pm_downloadaction' => array( 'DownloadAction' ),
			'pm_milestone' => array( 'Milestone' ),
		    'pm_projectrole' => array( 'ProjectRole' ),
			'pm_participantrole' => array( 'ParticipantRole'),
			'pm_changerequestlink' => array('RequestLink'),
		    'pm_changerequestlinktype' => array('RequestLinkType'),
		    'pm_accessright' => array('AccessRight'),
			'pm_version' => array( 'Release'),
			'pm_watcher' => array( 'Watcher' ),
			'pm_objectaccess' => array( 'AccessObject' ),
			'pm_issuetype' => array('RequestType'),
			'pm_changerequesttrace' => array('RequestTraceBase'),
			'pm_tasktrace' => array('TaskTraceBase'),
			'pm_customtag' => array('CustomTag'),
			'pm_importance' => array('Importance'),
			'pm_state' => array('StateBase'),
			'pm_transition' => array('Transition'),
			'pm_transitionrole' => array('TransitionRole'),
			'pm_transitionpredicate' => array('TransitionPredicate'),
			'pm_transitionattribute' => array('TransitionAttribute'),
			'pm_transitionresetfield' => array('TransitionResetField'),
			'pm_customreport' => array('PMCustomReport'),
			'pm_stateaction' => array( 'StateAction'),
			'pm_functiontrace' => array( 'FunctionTrace' ),
		    'pm_workspace' => array( 'Workspace'),
		    'pm_workspacemenu' => array( 'WorkspaceMenu' ),
		    'pm_workspacemenuitem' => array( 'WorkspaceMenuItem' ),
		    'pm_projectuse' => array( 'ProjectUse' ),
			'pm_featuretype' => array( 'FeatureType' ),
			'pm_stateattribute' => array( 'StateAttribute' )
		));
	}	
}