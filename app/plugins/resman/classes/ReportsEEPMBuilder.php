<?php

include_once SERVER_ROOT_PATH."pm/classes/report/ReportsBuilder.php";

class ReportsEEPMBuilder extends ReportsBuilder
{
    public function build( ReportRegistry & $object )
    {
        $module_it = getFactory()->getObject('Module')->getExact('resman/resourceload');
        if ( getFactory()->getAccessPolicy()->can_read($module_it) )
        {
            $object->addReport( array ( 
                'name' => 'resourceusage',
                'title' => $module_it->getDisplayName(),
                'description' => text('resman53'),
                'category' => FUNC_AREA_MANAGEMENT,
                'module' => $module_it->getId()
            ));

            $object->addReport( array ( 
                'name' => 'resourceavailability',
                'title' => text('resman114'),
            	'query' => 'format=graphical&viewpoint=roles',
                'description' => text('resman115'),
                'category' => FUNC_AREA_MANAGEMENT,
                'module' => $module_it->getId()
            ));
        }
    }
}