<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include "persisters/WikiPageBaselinesPersister.php";

class WikiPageModelBaselineBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
    	if ( !$object instanceof WikiPage ) return;

        $object->addAttribute('Baselines', 'REF_WikiPageBaselineId', text(2937), false);
        $object->addAttributeGroup('Baselines', 'trace-baselines');
		$object->addPersister(new WikiPageBaselinesPersister());
    }
}