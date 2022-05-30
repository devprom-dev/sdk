<?php
include_once "persisters/CustomAttributesObjectPersister.php";
include_once "sorts/CustomAttributeSortClause.php";

class CustomAttributesModelBuilder extends ObjectModelBuilder
{
    private $restrictedEntities = array(
        'pm_Project','pm_CustomAttribute','pm_ProjectLink','pm_Participant', 'pm_ParticipantRole', 'pm_ProjectRole',
        'cms_User'
    );

    public function build( Metaobject $object )
    {
        if ( in_array($object->getEntityRefName(), $this->restrictedEntities) ) return;

        $attributes = array_keys($object->getAttributes());
		$attr_it = getFactory()->getObject('pm_CustomAttribute')->getRegistry()->Query(
		    array(
		        new CustomAttributeEntityPredicate(get_class($object)),
                new FilterHasNoAttributePredicate('ReferenceName', $attributes),
                new CustomAttributeSortClause(array_shift($attributes))
            )
        );

    	while( !$attr_it->end() )
		{
            $object->addAttribute(
                $attr_it->get('ReferenceName'),
                $attr_it->getDbType(),
                $attr_it->get('Caption'),
                $attr_it->get('IsVisible') == 'Y',
                false, $attr_it->get('Description')
            );

			$object->setAttributeRequired($attr_it->get('ReferenceName'),
                $attr_it->get('ObjectKind') == '' ? $attr_it->get('IsRequired') == 'Y' : false);

            $object->setAttributeDefault($attr_it->get('ReferenceName'),
                $attr_it->get('ObjectKind') == '' ? $attr_it->getHtmlDecoded('DefaultValue') : '');

            $groups = $attr_it->getGroups();
            $object->setAttributeGroups($attr_it->get('ReferenceName'), $groups);
            $object->setAttributeOrigin($attr_it->get('ReferenceName'), ORIGIN_CUSTOM);
            $object->setAttributeEditable($attr_it->get('ReferenceName'), in_array('computed', $groups));

			$attr_it->moveNext();
		}

		if ( $attr_it->count() > 0 ) {
            $object->addPersister(new CustomAttributesPersister());
        }
    }
}