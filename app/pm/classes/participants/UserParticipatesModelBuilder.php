<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

include "UserParticipanceType.php";
include "predicates/UserParticipanceTypePredicate.php";
include "predicates/UserParticipanceRolePredicate.php";
include "predicates/UserParticipanceWorkloadPredicate.php";
include_once "predicates/UserParticipanceProjectPredicate.php";

class UserParticipatesModelBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'cms_User' ) return;
	
    	// system attributes

		$user_attributes = array (
		        'Caption',
		        'Email',
		        'Phone'
		);
		
		foreach( $object->getAttributes() as $attribute => $data )
		{
		    if ( in_array($attribute, $user_attributes) ) continue;
		    
		    $object->addAttributeGroup($attribute, 'system');
		    
		    $object->setAttributeVisible($attribute, false);
		}

		// project roles
		
		$object->addAttribute('ParticipantRole', 'REF_ParticipantRoleId', translate('Роль в проекте'), true, false, '', 100);
		
		$object->addAttribute('ProjectRole', 'REF_ProjectRoleId', translate('Роль'), false, false, '', 101);
		
		$object->addAttributeGroup('ProjectRole', 'system');

		$object->addAttribute('Capacity', 'FLOAT', translate('Ежедневная загрузка, ч.'), true, false, '', 102);
		
		$object->addAttribute('Project', 'REF_ProjectId', translate('Проект'), false, false, '', 103);
		
		$object->addAttribute('Participant', 'REF_ParticipantId', translate('Участник'), false, false, '', 104);

		$object->addAttributeGroup('Participant', 'system');

		$object->addAttribute('ParticipanceType', 'REF_UserParticipanceTypeId', translate('Участие'), false, false, '', 105);
		
		$object->addAttributeGroup('ParticipanceType', 'system');
		
		$object->addPersister( new UserParticipatesDetailsPersister() );
    }
}