<?php

include_once SERVER_ROOT_PATH."pm/classes/report/ReportsBuilder.php";

class ReportsSourceControlBuilder extends ReportsBuilder
{
    public function build( ReportRegistry & $object )
    {
        global $model_factory;
        
        $module = $model_factory->getObject('Module');
        
        $module_it = $module->getExact('sourcecontrol/revision');
        
        if ( getFactory()->getAccessPolicy()->can_read($module_it) )
        {
            $object->addReport( array ( 
                    'name' => 'scm-revisions', 
                    'description' => "text(sourcecontrol6)",
                    'module' => $module_it->getId(), 
                    'category' => ModuleCategoryBuilderCode::AREA_UID 
            ));

            $object->addReport( array ( 
                    'name' => 'scm-commitsperauthors',
                    'title' => "text(sourcecontrol23)",
                    'description' => text('sourcecontrol31'),
                    'module' => $module_it->getId(),
                    'query' => 'aggregator=none&aggby=Author&group=Repository&view=chart',
                    'category' => ModuleCategoryBuilderCode::AREA_UID,
                    'type' => 'chart' 
            ));
    
            $object->addReport( array ( 
                    'name' => 'commitsfreqperauthors',
                    'title' => "text(sourcecontrol24)",
                    'description' => text('sourcecontrol32'),
                    'module' => $module_it->getId(),
                    'query' => 'aggregator=COUNT&aggby=Author&group=history&view=chart',
                    'category' => ModuleCategoryBuilderCode::AREA_UID,
                    'type' => 'chart' 
            ));
        }

        $module_it = $module->getExact('sourcecontrol/files');
        
        if ( getFactory()->getAccessPolicy()->can_read($module_it) )
        {
            $object->addReport( array ( 
                    'name' => 'scm-files', 
                    'description' => text('sourcecontrol30'),
                    'module' => $module_it->getId(), 
                    'category' => ModuleCategoryBuilderCode::AREA_UID 
            ));
        }
    }
}