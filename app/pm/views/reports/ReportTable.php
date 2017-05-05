<?php

include "ReportList.php";

class ReportTable extends PMPageTable
{
	function getList()
	{
		return new ReportList( $this->getObject() );
	}

	function getFilters()
	{
		return array(
		    $this->buildSearchFilter(),
			$this->buildTypeFilter(),
			$this->buildCategoryFilter(),
			new ViewReportTypeWebMethod()
		); 		
	}

	function buildSearchFilter() {
		$search = new FilterTextWebMethod( text(1329), 'search-keywords' );
		$search->setScript( 'filterReports( $(this).val() )' );
		$search->setStyle( 'width:340px' );
		return $search;
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

	function getFilterPredicates()
	{
		$values = $this->getFilterValues();
		
		return array_merge(
			parent::getFilterPredicates(),
			array (
				new FilterAttributePredicate( 'Category', $values['pmreportcategory'] ),
				new FilterAttributePredicate( 'Type', $values['reporttype'] ),
				new FilterAttributePredicate( 'IsCustomized', $values['type'] )
			)
		);
	}
	
	function getFiltersDefault()
	{
	    return array('search-keywords', 'reporttype', 'pmreportcategory');
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

    function drawScripts()
	{
	    parent::drawScripts();
	    
	    ?>
	    <script type="text/javascript">
            var keywords_stored = '';

            $().ready( function() {
                window.setInterval(function() {
                    if ( $('input[valueparm="search-keywords"]').val() != keywords_stored ) {
                        keywords_stored = $('input[valueparm="search-keywords"]').val();
                        $('input[valueparm="search-keywords"]').trigger('onchange');
                    }
                }, 200);
            });
        </script>
	    
	    <?php
	}
} 