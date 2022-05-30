<?php
include SERVER_ROOT_PATH . "pm/methods/ViewReportTypeWebMethod.php";
include "ReportList.php";

class ReportTable extends PMPageTable
{
	function getList() {
		return new ReportList( $this->getObject() );
	}

	function getFilters()
	{
		return array(
			$this->buildTypeFilter(),
			$this->buildCategoryFilter(),
			new ViewReportTypeWebMethod()
		); 		
	}

	function buildCategoryFilter() {
		$category = new FilterObjectMethod( getFactory()->getObject('PMReportCategory'), translate('Раздел') );
		$category->setIdFieldName('ReferenceName');
		$category->setHasNone(false);
		return $category;
	}

	function buildTypeFilter() {
		$category = new FilterObjectMethod( getFactory()->getObject('ReportType'), translate('Категория') );
		$category->setHasNone(false);
		return $category;
	}

	function getFilterPredicates( $values )
	{
		return array_merge(
			parent::getFilterPredicates( $values ),
			array (
				new FilterAttributePredicate( 'Category', $values['pmreportcategory'] ),
				new FilterAttributePredicate( 'Type', $values['reporttype'] ),
				new FilterAttributePredicate( 'IsCustomized', $values['type'] )
			)
		);
	}
	
	function getSortFields()
	{
		return array();
	}
	
	function getSortDefault( $parm )
	{
		return '';
	}
	
	function getNewActions()
	{
		return array();
	}

	function getExportActions()
	{
		return array();
	}

	function getBulkActions()
	{
		$action = new BulkAction(getFactory()->getObject('PMCustomReport'));
		$action_it = $action->getAll();
        $url = '?formonly=true';
		$action_url = "javascript:processBulk('".$action_it->get('Caption')."','".$url.'&operation='.$action_it->getId()."');";
		return array (
			'delete' => array(
                array (
                    'uid' => 'bulk-delete',
                    'name' => $action_it->get('Caption'),
                    'url' => $action_url
                )
			)
		);
	}

	function getRowsOnPage() {
        return 9999;
    }

	function getRenderParms($parms)
    {
        $parms = parent::getRenderParms($parms);
        $parms['filter_search']['script'] = "filterReports( $(this).val() )";
        return $parms;
    }

    function getDetails() {
        return array();
    }
} 