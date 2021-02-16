<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include_once "persisters/ObjectWeekDatesPersister.php";

class DateYearWeekModelBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
		$object->addAttribute( 'WeekCreated', 'REF_DateYearWeekId', text(2049), false );
        $object->addAttribute( 'WeekModified', 'REF_DateYearWeekId', text(2050), false );
		if ( $object->getAttributeType('FinishDate') != '') {
            $object->addAttribute( 'WeekFinished', 'REF_DateYearWeekId', text(2537), false );
        }
	    $object->addPersister( new ObjectWeekDatesPersister() );
    }
}