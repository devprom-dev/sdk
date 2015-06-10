<?php

include_once SERVER_ROOT_PATH."pm/classes/widgets/FunctionalAreaMenuBuilder.php";

class FunctionalAreaMenuPortfolioResManBuilder extends FunctionalAreaMenuBuilder
{
    public function build( FunctionalAreaMenuRegistry & $set )
    {
    	if ( !getSession()->getProjectIt() instanceof PortfolioIterator ) return;
    	 
        $menus = $set->getAreaMenus( FUNC_AREA_FAVORITES );
        if ( count($menus) < 1 ) return;
        if ( getSession()->getProjectIt()->get('CodeName') == 'my' ) return;

        $report = getFactory()->getObject('PMReport');
        $menus['resources']['items'] = array_merge( 
        		array( $report->getExact('resourceavailability')->buildMenuItem('?') ),
        		array( $report->getExact('resourceusage')->buildMenuItem('?') ),
                $menus['resources']['items']
        );

 		$set->setAreaMenus( FUNC_AREA_FAVORITES, $menus );
    }
}