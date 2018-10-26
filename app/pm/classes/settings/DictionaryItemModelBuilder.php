<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class DictionaryItemModelBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
        $object->setAttributeVisible('OrderNum',
            $object instanceof StateBase
            || $object instanceof FeatureType
            || $object instanceof RequestType
            || $object instanceof Transition
            || $object instanceof WikiTypeBase
        );
        if ( $object->IsAttributeVisible('OrderNum') ) {
            $object->setSortDefault(
                array(
                    new SortOrderedClause()
                )
            );
        }
	}
}