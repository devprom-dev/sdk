<?php

include_once SERVER_ROOT_PATH."pm/classes/widgets/FunctionalAreaBuilder.php";

class FunctionalAreaFileServerBuilder extends FunctionalAreaBuilder
{
    public function build( FunctionalAreaRegistry & $set )
    {
 	    $project_it = getSession()->getProjectIt();
 	    
 	    if ( $project_it instanceof PortfolioIterator ) return;
 	    
 	    if ( $project_it->getMethodologyIt()->get('IsFileServer') != 'Y' ) return;
        
        $set->addArea( ModuleCategoryBuilderFileServer::AREA_UID );
    }
}