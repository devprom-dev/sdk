<?php
include_once SERVER_ROOT_PATH."pm/classes/common/persisters/EntityProjectPersister.php";

class ProjectArtifactMetadataBuilder extends ObjectMetadataBuilder
{
    private $skipEntities = array (
        'pm_Workspace', 'pm_WorkspaceMenu', 'pm_WorkspaceMenuItem', 'pm_StateObject', 'pm_Watcher', 'cms_Resource',
        'pm_Methodology', 'pm_ProjectRole', 'pm_ParticipantRole', 'pm_AccessRight', 'co_AccessRight', 'pm_ObjectAccess',
        'pm_CustomAttribute', 'pm_IssueType', 'pm_TaskType', 'WikiPageType', 'pm_StateAttribute', 'pm_StateAction', 'pm_Transition',
        'pm_TransitionAction', 'pm_TransitionPredicate', 'entity', 'ObjectChangeLog',
        'pm_TransitionRole', 'pm_FeatureType', 'pm_UserSettings', 'pm_CustomReport', 'pm_ProjectLink'
    );

    public function build( ObjectMetadata $metadata )
    {
        if ( in_array($metadata->getObject()->getEntityRefName(), $this->skipEntities) ) return;
        if ( getFactory()->getEntityOriginationService()->getSelfOrigin($metadata->getObject()) == '' ) return;

		if ( $metadata->getAttributeType('Project') == '' ) {
			$metadata->addAttribute('Project', 'REF_pm_ProjectId', translate('Проект'), false);
		}
		$metadata->addPersister( new EntityProjectPersister );
    }
}