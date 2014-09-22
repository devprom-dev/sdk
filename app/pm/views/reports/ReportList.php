<?php

use Devprom\ProjectBundle\Service\Navigation\WorkspaceService;

class ReportList extends PageList
{
	private $first_area_it = null;
	
	private $favorite_it = null;
	
    function getIterator()
    {
        $object = $this->getObject();
        
        $predicates = $this->getPredicates( $this->getFilterValues() );
        		
 	    $report_category_filter_values = array();
 	    
 	 	foreach( $predicates as $key => $filter )
 		{
 			if ( is_a( $filter, 'PMReportCategoryPredicate') && $filter->getValue() != '' )
 			{
 				$report_category_filter_values = preg_split('/,/', $filter->getValue());
 			}
 		}
 		
 		$it = $object->getAll();
        
 		$rowset = $it->getRowset();
		
 		if ( count($report_category_filter_values) > 0 && !in_array('all', $report_category_filter_values) )
 		{
 		    foreach( $rowset as $row => $report )
 		    {
 		     	if ( in_array('none', $report_category_filter_values) && $report['Category'] != '' ) 
 				{
 					unset($rowset[$row]); continue;
 				} 
				if ( !in_array($report['Category'], $report_category_filter_values) )
				{
					unset($rowset[$row]); continue;
				}
 		    }
 		}

 		if ( $object->getSystemOnly() )
		{
	 		foreach( $rowset as $row => $report )
			{
				if ( $report['IsCustomized'] == 'Y' ) unset($rowset[$row]);
 			}
		}
		else if ( $object->getUsersOnly() )
		{
		    foreach( $rowset as $row => $report )
		    {
		        if ( $report['IsCustomized'] != 'Y' ) unset($rowset[$row]);
		    }
		}

		$it = $object->createCachedIterator( array_values($rowset) );
		
		$service = new WorkspaceService();
		
		$this->favorite_it = $service->getItemOnFavoritesWorkspace($it->fieldToArray('cms_ReportId'));

		return $it;
    }
    
	function IsNeedToDisplay( $attr ) 
	{
		switch ( $attr )
		{
			case 'Caption':
				return true;
				
			default:
				return false;
		}
	}

	function IsNeedToDeleteRow( $object_it )
	{
		return is_numeric($object_it->getId()) && parent::IsNeedToDeleteRow( $object_it );
	}

	function getColumnFields()
	{
		$fields = parent::getColumnFields();
		
		unset($fields[array_search('Url', $fields)]);
		
		return $fields;
	}
	
	function drawGroup( $group_field, $object_it ) 
	{
		global $model_factory;
		
		switch ( $group_field )
		{
			case 'Category':
				
			    $category = $model_factory->getObject('PMReportCategory');

			    $category_it = $category->getByRef( 'ReferenceName', $object_it->get('Category') );

			    if ( $category_it->getId() )
			    {
				    echo $category_it->getDisplayName();
			    }
			    else
			    {
				    echo translate('Отчеты');
			    }
				
				break;
		}
	}
	
	function getItemActions( $column_name, $object_it ) 
	{
		$actions = array();

		$this->favorite_it->moveTo('UID', $object_it->getId());

		if ( $this->favorite_it->getId() == '' )
		{
			$info = $object_it->buildMenuItem();
			
		    $actions[] = array(
			    'name' => text(1327),
			    'url' => "javascript:addToFavorites('".$object_it->getId()."','".urlencode($info['url'])."');" 
			);
		}
		
		if ( is_numeric($object_it->getId()) )
		{
			$custom_it = getFactory()->getObject('pm_CustomReport')->getExact( $object_it->getId() );

			$method = new ObjectModifyWebMethod($custom_it);
			
			if ( $method->hasAccess() )
			{
			    if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
			    
				$actions[] = array(
				    'url' => $method->getJSCall(), 
				    'name' => translate('Изменить')
				);
			}
			
			$method = new DeleteObjectWebMethod( $custom_it );
			
			if ( $method->hasAccess() )
			{
			    if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
			    
				$actions[] = array(
				    'url' => $method->getJSCall(), 
				    'name' => $method->getCaption()
				);
			}
		}

		return $actions;
	}

	function drawCell( $object_it, $attr ) 
	{
		global $project_it;
		
		switch ( $attr )
		{
			case 'Caption':

			    echo '<div>';
    				if ( $object_it->get('Type') == 'chart' )
    				{
    					echo '<img style="height:18px;" src="/images/chart_line.png">';
    				}
    				else
    				{
    					echo '<img style="height:18px;" src="/images/table.png">';
    				}
    				
    				$url = $object_it->getUrl().($_REQUEST['pmreportcategory'] != '' ? '&pmreportcategory='.SanitizeUrl::parseUrl($_REQUEST['pmreportcategory']) : '');
    				
    				echo '<a href="'.$url.'" style="font-weight:bold;padding-left:12px;">'.$object_it->getDisplayName().'</a>';
    			echo '</div>';
    			
			    echo '<div style="padding-top:8px">';
			        echo $object_it->get('Description');
    			echo '</div>';
			    break;
			default:
				parent::drawCell( $object_it, $attr );
		}
	}
	
	function IsNeedToDisplayNumber()
	{
		return false;
	}
	
	protected function getFirstAreaIt()
	{
		if ( is_object($this->first_area_it) ) return $this->first_area_it;
		
		return $this->first_area_it = getFactory()->getObject('FunctionalArea')->getAll();
	}
}