<?php
include_once SERVER_ROOT_PATH."pm/classes/common/persisters/EntityProjectPersister.php";

class ProjectArtifactMetadataBuilder extends ObjectMetadataBuilder
{
	private $artifacts = array(
		'pm_Task', 'pm_ChangeRequest', 'WikiPage', 'Comment', 'pm_Question', 'pm_Test',
		'pm_Artefact', 'pm_Attachment', 'WikiPageFile', 'BlogPost', 'BlogPostFile',
		'pm_Milestone', 'pm_Function', 'pm_Meeting', 'pm_SubversionRevision',
		'sm_Aim', 'sm_Person', 'sm_Action', 'sm_Activity',
		'pm_State', 'pm_Transition', 'pm_Activity', 'pm_Release', 'pm_Version',
		'cms_Snapshot'
	);

    public function build( ObjectMetadata $metadata )
    {
		if ( !in_array($metadata->getObject()->getEntityRefName(),$this->artifacts) ) return;

		if ( $metadata->getAttributeType('Project') == '' ) {
			$metadata->addAttribute('Project', 'REF_pm_ProjectId', translate('Проект'), false);
		}
		$metadata->addPersister( new EntityProjectPersister );
    }
}