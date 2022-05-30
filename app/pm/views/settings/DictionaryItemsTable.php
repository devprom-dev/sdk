<?php
include "DictionaryItemsList.php";
include "FormFieldList.php";

class DictionaryItemsTable extends SettingsTableBase
{
	function getList()
	{
        switch ( $this->getObject()->getEntityRefName() ) {
            case 'pm_StateAttribute':
                return new FormFieldList($this->getObject());
            default:
                return new DictionaryItemsList($this->getObject());
        }
	}

	function getSortDefault( $sort_parm = 'sort' )
	{
	    if ( $sort_parm == 'sort' ) {
	        return 'OrderNum';
        }
	    return parent::getSortDefault( $sort_parm );
	}
	
	function getFilters()
	{
		switch ( $this->getObject()->getClassName() )
		{
			case 'pm_CustomAttribute':
			    $filter = new FilterObjectMethod(
			        getFactory()->getObject('CustomizableObjectSet'),
                        translate('Сущность'), 'customattributeentity');
			    $filter->setHasNone(false);
			    $filter->setIdFieldName('ReferenceName');
				return array ( $filter );

			default:
				return parent::getFilters();
		}
	}
	
	function getNewActions()
	{
		$actions = array();

		switch ( $this->getObject()->getClassName() )
		{
			case 'pm_CustomAttribute':
				
				$values = $this->getFilterValues();
				$items = preg_split('/,/',$values['customattributeentity']);
				$entity_ref_name = array_shift($items); 				

				$parms = array(
                    'area' => $this->getPage()->getArea(),
                    'redirect' => $_SERVER['REQUEST_URI']
                );
				if ( $entity_ref_name != '' ) {
				    $parms['EntityReferenceName'] = $entity_ref_name;
                }

				$actions[] = array (
						'name' => translate('Добавить'),
						'url' => $this->getObject()->getPageName().'&'.http_build_query($parms)
				);

				return $actions;
				
			default:
				return parent::getNewActions();
		}
	}
	
 	function getFilterPredicates( $values )
 	{
		switch ( $this->getObject()->getClassName() )
		{
			case 'pm_CustomAttribute':
				return array_merge(
				    parent::getFilterPredicates( $values ),
                    array (
                        new CustomAttributeEntityPredicate( $values['customattributeentity'] ),
                        new FilterBaseVpdPredicate()
				    )
                );
		}
		
		return array_merge( 
            parent::getFilterPredicates( $values ),
            array (
                new FilterBaseVpdPredicate()
            )
		);
 	}

    function getActions()
    {
        $actions = parent::getActions();
        $module_it = getFactory()->getObject('Module')->getExact('process/storesettings');
        if ( getFactory()->getAccessPolicy()->can_read($module_it) ) {
            $actions[] = array();
            $actions[] = array (
                'name' => $module_it->getDisplayName(),
                'url' => $module_it->getUrl()
            );
        }
        return $actions;
    }

    function getImportActions() {
	    if ( $this->getObject() instanceof PMCustomAttribute ) return array();
	    return parent::getImportActions();
    }

    protected function buildProjectFilter() {
        return null;
    }
}