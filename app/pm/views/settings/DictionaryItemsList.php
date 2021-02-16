<?php

class DictionaryItemsList extends PMPageList
{
    function extendModel()
    {
        parent::extendModel();

        switch ( $this->object->getClassName() ) {
            case 'pm_IssueType':
                $this->object->setRegistry( new ObjectRegistrySQL($this->object) );
                break;
        }
    }

    function IsNeedToDisplayLinks( ) { return false; }
	
	function IsNeedToDisplay( $attr ) 
	{
		switch ( $this->object->getClassName() )
		{
			case 'pm_ProjectRole':
			case 'pm_TaskType':
		 		switch( $attr ) 
		 		{
		 			default:
 						return parent::IsNeedToDisplay( $attr );
		 		}
		 		break;
 				
			case 'pm_CustomAttribute':
		 		switch( $attr ) 
		 		{
		 			case 'DefaultValue':
		 			case 'IsVisible':
		 			case 'IsRequired':
		 			case 'IsUnique':
		 				return false;
		 				
		 			default:
 						return parent::IsNeedToDisplay( $attr );
		 		}
		 		break;
		 		
			default:
 				return parent::IsNeedToDisplay( $attr );
		}
	}

	function drawCell( $object_it, $attr )
	{
		switch ( $attr )
		{
			case 'EntityReferenceName':
			    if ( getSession()->IsRDD() && $object_it->get($attr) == 'request' ) {
                    echo translate('Доработка');
                }
			    else {
                    echo $object_it->getEntityDisplayName();
                }
				break;
				
			default:
				parent::drawCell( $object_it, $attr );
		}		
	}
	
	function getGroupDefault()
	{
	    return 'none';
	}
}
