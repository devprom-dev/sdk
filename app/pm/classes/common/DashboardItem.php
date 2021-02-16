<?php
include "DashboardItemIterator.php";

class DashboardItem extends Metaobject
{
    function __construct() {
        parent::__construct('pm_DashboardItem');
        $this->addAttributeGroup('WidgetUID', 'alternative-key');
        $this->setSortDefault(
            array(
                new SortOrderedClause()
            )
        );
    }

    function createIterator() {
        return new DashboardItemIterator( $this );
    }

    function add_parms($parms)
    {
        if ( $parms['Height'] == '' ) $parms['Height'] = 300;
        if ( $parms['Width'] == '' ) $parms['Width'] = 600;

        return parent::add_parms($parms);
    }

    function modify_parms( $id, $parms )
    {
        $objectIt = $this->getExact($id);
        $result = parent::modify_parms($id, $parms);

        DAL::Instance()->Query("SET @r=0");
        DAL::Instance()->Query("
            UPDATE pm_DashboardItem t SET t.OrderNum = @r:= (@r+1) 
             WHERE t.VPD = '".$objectIt->get('VPD')."' ORDER BY t.OrderNum ASC, t.RecordModified DESC
        ");

        return $result;
    }

    function getPage() {
        return getSession()->getApplicationUrl($this).'dashboard?';
    }

    function getPageNameViewMode( $objectid ) {
        return $this->getPage();
    }
}
