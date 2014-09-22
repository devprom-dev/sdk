<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class TransitionModelBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( ! $object instanceof Transition ) return;

		$object->addAttribute('Attributes', 
			'REF_TransitionAttributeId', translate('������������ ����'), false);

		$object->addAttribute('ProjectRoles', 
			'REF_TransitionRoleId', translate('��������� ����'), false);

		$object->addAttribute('Predicates', 
			'REF_TransitionPredicateId', translate('�����������'), false);

		$object->addAttribute('ResetFields', 
			'REF_TransitionResetFieldId', translate('��������� ����'), false);
    }
}