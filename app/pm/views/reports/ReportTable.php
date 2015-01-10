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
		global $model_factory;
		
		$search = new FilterTextWebMethod( text(1329), 'search-keywords' );
		
		$search->setScript( 'filterReports( $(this).val() )' );
		
		$search->setStyle( 'width:340px' );
		
		$category = new FilterObjectMethod( $model_factory->getObject('PMReportCategory'), translate('Раздел') );
		
		$category->setIdFieldName('ReferenceName');
		$category->setHasNone(false);
		
		return array(
		    $search, 
			$category, 
			new ViewReportTypeWebMethod()
		); 		
	}

	function getFilterPredicates()
	{
		$values = $this->getFilterValues();
		
		switch ( $values['type'] )
		{
		    case 'user':
		    	
		    	$this->getObject()->setUsersOnly();
		    	
		    	break;
		    	
		    case 'system':
		    	
		    	$this->getObject()->setSystemOnly();
		    	
		    	break;
		}

		return array_merge( parent::getFilterPredicates(), array ( 
				new PMReportCategoryPredicate( $values['pmreportcategory'] )
		));
	}
	
	function getFiltersDefault()
	{
	    return array('search-keywords', 'pmreportcategory');
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
	
	function getDeleteActions()
	{
		$actions = array();
		
		$method = new BulkDeleteWebMethod();
		
		$method->setRedirectUrl('');
		
		$actions[] = array ( 
				'name' => $method->getCaption(),
				'url' => $method->getJSCall( getFactory()->getObject('PMCustomReport') ),
				'title' => $method->getDescription() 
		);

		return $actions;
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