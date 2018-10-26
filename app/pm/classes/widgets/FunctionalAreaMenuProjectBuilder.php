<?php

include_once "FunctionalAreaMenuBuilder.php";

class FunctionalAreaMenuProjectBuilder extends FunctionalAreaMenuBuilder
{
    public function build( FunctionalAreaMenuRegistry & $set )
    {
        $items = array ();
        $report = getFactory()->getObject('PMReport');

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