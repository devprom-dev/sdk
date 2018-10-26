<?php

use Devprom\ProjectBundle\Service\Navigation\WorkspaceService;

class ReportList extends PMPageList
{
	private $first_area_it = null;

	private $favorite_reports = array();
	
    function getIterator()
    {
        $it = $this->getObject()->getRegistry()->Query(
            $this->getPredicates( $this->getFilterValues() )
        );

        $service = new WorkspaceService();
        $this->favorite_reports = $service->getItemOnFavoritesWorkspace($it->fieldToArray('cms_ReportId'));

        return $it;
    }

    function getForm( $object_it ) {
        return null;
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

	function getGroupFields()
	{
		return array('Category');
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

		$id = $object_it->getId();
		$filtered = array_filter($this->favorite_reports, function($value) use ($id) {
			return $value['id'] == $id;
		});

		if ( count($filtered) < 1 )
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
			if ( $method->hasAccess() ) {
			    if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
				$actions[] = array(
				    'url' => $method->getJSCall(),
				    'name' => $method->getCaption()
				);
			}

			$method = new DeleteObjectWebMethod( $custom_it );
			if ( $method->hasAccess() ) {
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
    			
			    echo '<div class="help-block" style="padding-top:12px">';
			        echo $object_it->get('Description');
    			echo '</div>';
			    break;
			default:
				parent::drawCell( $object_it, $attr );
		}
	}
	
	protected function getFirstAreaIt()
	{
		if ( is_object($this->first_area_it) ) return $this->first_area_it;
		
		return $this->first_area_it = getFactory()->getObject('FunctionalArea')->getAll();
	}
}