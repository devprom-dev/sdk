<?php

class WikiTemplateList extends PMPageList
{
  	function setupColumns()
 	{
 	    $this->getObject()->setAttributeOrderNum( 'Content', 0 );

        $this->getObject()->setAttributeCaption( 'UserField1', translate('Шаблон по умолчанию') );
 	    
		parent::setupColumns();
 	}
    
	function IsNeedToDelete()
	{
		return false;
	}
 	
	function IsNeedToDisplayNumber()
	{
	    return false;
	}
	
	function IsNeedToDisplayOperations()
	{
	    return false;
	}
	
	function drawCell( $object_it, $attr ) 
	{
		switch ( $attr ) 
		{
			case 'UserField1':
			    
				echo ($object_it->get($attr) == 'Y' ? translate('Да') : translate('Нет'));
				
				break;
				
			case 'Content':
			    
        		$this->getTable()->getForm()->setFormIndex( $object_it->getId() );

        		$this->getTable()->getForm()->show( $object_it->getId() );
        		
        		$this->getTable()->getForm()->setPage( $this->getTable()->getPage() );
        		
        		$this->getTable()->getForm()->setReviewMode();
        		
        		$this->getTable()->getForm()->render( $this->getTable()->getView(), array());

        		echo '<hr/>';
        		break;

			default:
				
				parent::drawCell( $object_it, $attr );			
		}
	}

 	function getRenderParms()
 	{
		$parent_parms = parent::getRenderParms();

		return array_merge( $parent_parms, array (
		    'table_class_name' => 'table-document' 
 	    ));
 	}
 	
	function getGroupFields()
	{
		return array();
	}
}