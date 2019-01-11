<?php
include_once "persisters/CustomAttributesObjectPersister.php";
include_once "sorts/CustomAttributeSortClause.php";

class CustomAttributesModelBuilder extends ObjectModelBuilder
{
    private $objectIt = null;

    function __construct($objectIt) {
        $this->objectIt = $objectIt;
    }

    public function build( Metaobject $object )
    {
		$attr_it = getFactory()->getObject('pm_CustomAttribute')->getRegistry()->Query(
		    array(
		        new CustomAttributeObjectPredicate($this->objectIt),
                new CustomAttributeSortClause(),
                new FilterHasNoAttributePredicate('ReferenceName', array_keys($object->getAttributes()))
            )
        );
		
    	while( !$attr_it->end() )
		{
            $db_type = $attr_it->getDBType();
            $groups = $attr_it->getGroups();

			if ( $attr_it->get('ShowMainTab') != 'Y' && !in_array('trace', $groups) ) {
                $groups[] = 'additional';
			}

            $object->addAttribute(
                $attr_it->get('ReferenceName'), $db_type,
                $attr_it->get('Caption'), $attr_it->get('IsVisible') == 'Y',
                false, $attr_it->get('Description')
            );

			$object->setAttributeRequired($attr_it->get('ReferenceName'),
                $attr_it->get('ObjectKind') == '' ? $attr_it->get('IsRequired') == 'Y' : false);

            $object->setAttributeDefault($attr_it->get('ReferenceName'),
                $attr_it->get('ObjectKind') == '' ? $attr_it->getHtmlDecoded('DefaultValue') : '');

            $object->setAttributeGroups($attr_it->get('ReferenceName'), $groups);
            $object->setAttributeOrigin($attr_it->get('ReferenceName'), ORIGIN_CUSTOM);

			$attr_it->moveNext();
		}

		if ( $attr_it->count() > 0 ) {
    	    $persister = new CustomAttributesObjectPersister();
            $persister->setObjectIt($this->objectIt);
            $object->addPersister($persister);
        }
    }
}