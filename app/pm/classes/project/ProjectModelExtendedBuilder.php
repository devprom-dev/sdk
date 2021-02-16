<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include "persisters/ProjectDashboardPersister.php";

class ProjectModelExtendedBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'pm_Project' ) return;

        $object->setAttributeVisible('StartDate', true);
        $object->setAttributeVisible('FinishDate', true);

        $object->addAttribute( 'Features', 'REF_FeatureId', text(2613), false );
        $object->addAttribute( 'SpentHours', 'INTEGER', text(2614), false );
        $object->addAttributeGroup('SpentHours', 'hours');
        $object->addAttribute( 'SpentHoursWeek', 'INTEGER', text(2615), false );
        $object->addAttributeGroup('SpentHoursWeek', 'hours');
        $object->addPersister( new ProjectDashboardPersister() );

        foreach( array('WikiEditorClass','DaysInWeek', 'Rating', 'ProjectKey') as $attribute ) {
            $object->addAttributeGroup($attribute, 'additional');
        }
    }
}