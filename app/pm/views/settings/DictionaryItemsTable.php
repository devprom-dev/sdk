<?php

include "DictionaryItemsList.php";

class DictionaryItemsTable extends PMPageTable
{
	function getList()
	{
		return new DictionaryItemsList( $this->getObject() );
	}

    function getCaption()
    {
        return $this->getObject()->getDisplayName();
    }
	
	function getSortDefault( $sort_parm = 'sort' )
	{
	    if ( $sort_parm == 'sort' ) return 'OrderNum';
		
	    return parent::getSortDefault( $sort_parm );
	}
	
	function getFilters()
	{
	    global $model_factory;

		switch ( $this->getObject()->getClassName() )
		{
			case 'pm_CustomAttribute':
			    
			    $filter = new FilterObjectMethod($model_factory->getObject('CustomizableObjectSet'), translate('Сущность'), 'customattributeentity');
			    
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
				
				$actions[] = array ( 
						'name' => translate('Добавить'),
						'url' => $this->getObject()->getPageName().'&area='.$this->getPage()->getArea().'&redirect='.$_SERVER['REQUEST_URI']
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
		}		
		
		return array_merge( 
				parent::getFilterPredicates(),
				array (
						new FilterBaseVpdPredicate()
				)
		);
 	}
} 