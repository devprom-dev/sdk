<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class ProjectModelDashboardBuilder extends ObjectModelBuilder
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'pm_Project' ) return;
    }
}