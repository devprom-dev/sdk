<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/plan/persisters/ReleaseArtefactsPersister.php";

class ReleaseModelArtefactsBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
		if ( !$object instanceof Release ) return;

        if ( getSession()->IsRDD() ) {
            $object->addAttribute('Issues', 'REF_IncrementId', text(1805), false, false, '', 90);
        }
        else {
            $object->addAttribute('Issues', 'REF_pm_ChangeRequestId', text(808), false, false, '', 90);
        }
		$object->addAttributeGroup('Issues', 'tabissues');

		$object->addPersister( new ReleaseArtefactsPersister() );
    }
}