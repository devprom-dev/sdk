<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class IssueAutoActionModelBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
    	if ( ! $object instanceof IssueAutoAction ) return;

        $importantAttributes = array('State', 'Project');
        $this->subject = getFactory()->getObject($object->getSubjectClassName());

        foreach( $object->getActionAttributes() as $attribute )
        {
            if ( $this->subject->getAttributeType($attribute) == '' ) continue;
            if ( !$this->subject->IsAttributeVisible($attribute) && !in_array($attribute, $importantAttributes) ) continue;

            $object->addAttribute(
                $attribute,
                $attribute == 'State'
                    ? 'REF_' . $this->subject->getStateClassName() . 'Id'
                    : $this->subject->getAttributeDbType($attribute),
                $this->subject->getAttributeUserName($attribute),
                true,
                false
            );
            $groups = $this->subject->getAttributeGroups($attribute);
            if ( is_array($groups) ) {
                $object->setAttributeGroups($attribute, array_filter($groups, function($group) {
                    return !in_array($group, array('workflow'));
                }));
            }
            $object->setAttributeOrigin($attribute, $object->getAttributeOrigin($attribute));
            $object->addAttributeGroup($attribute, 'actions');
            $object->addAttributeGroup($attribute, 'system');
        }
    }
}