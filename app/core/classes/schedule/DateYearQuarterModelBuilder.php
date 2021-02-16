<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include_once "persisters/ObjectQuarterDatesPersister.php";

class DateYearQuarterModelBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
		$object->addAttribute( 'QuarterCreated', 'REF_DateYearQuarterId', text(2814), false );
        $object->addAttribute( 'QuarterModified', 'REF_DateYearQuarterId', text(2813), false );
		if ( $object->getAttributeType('FinishDate') != '' ) {
            $object->addAttribute( 'QuarterFinished', 'REF_DateYearQuarterId', text(2815), false );
        }
	    $object->addPersister( new ObjectQuarterDatesPersister() );
    }
}