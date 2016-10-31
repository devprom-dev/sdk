<?php

include_once "FunctionalAreaMenuBuilder.php";

class FunctionalAreaMenuProjectBuilder extends FunctionalAreaMenuBuilder
{
    public function build( FunctionalAreaMenuRegistry & $set )
    {
        $report = getFactory()->getObject('PMReport');
        $item = $report->getExact('navigation-settings')->buildMenuItem();
        $item['order'] = 9998;

        $items = array (
            'navigation-settings' => $item
        );

        $uid = $this->getAreaUid();
        if ( $uid != '' ) {
            $items['charts'] = $report->getExact('charts')->buildMenuItem('pmreportcategory='.$uid);
            $items['charts']['order'] = 9999;
        }

        $menus = array();
   		$menus['quick'] = array(
           'name' => '',
           'items' => $items,
           'uid' => 'quick'
   		);
		return $menus;
    }

    protected function getAreaUid() {
        return '';
    }
}