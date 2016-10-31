<?php

include "DictionaryItemsList.php";

class DictionaryItemsTable extends PMPageTable
{
	function __construct( $object )
	{
		$object->setAttributeVisible('OrderNum', $object instanceof StateBase || $object instanceof FeatureType);
		
		parent::__construct($object);
	}
	
	function getList()
	{
		return new DictionaryItemsList( $this->getObject() );
	}

	function getSortDefault( $sort_parm = 'sort' )
	{
	    if ( $sort_parm == 'sort' ) return 'OrderNum';
		
	    return parent::getSortDefault( $sort_parm );
	}
	
	function getFilters()
	{
		switch ( $this->getObject()->getClassName() )
		{
			case 'pm_CustomAttribute':
			    $filter = new FilterObjectMethod(getFactory()->getObject('CustomizableObjectSet'), translate('Сущность'), 'customattributeentity');
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

				$actions[] = array ( 
						'name' => translate('Добавить'),
						'url' => $this->getObject()->getPageName().'&EntityReferenceName='.$entity_ref_name.
										'&area='.$this->getPage()->getArea().'&redirect='.$_SERVER['REQUEST_URI']
				);
				
				return $actions;
				
			default:
				return parent::getNewActions();
		}
	}
	
 	function getFilterPredicates()
 	{
 	    $values = $this->getFilterValues();
 	    
		switch ( $this->getObject()->getClassName() )
		{
			case 'pm_CustomAttribute':
				return array_merge( parent::getFilterPredicates(), array (
						new CustomAttributeEntityPredicate( $values['customattributeentity'] )
				));

			case 'pm_Environment':
				return parent::getFilterPredicates();
		}		
		
		return array_merge( 
				parent::getFilterPredicates(),
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
}