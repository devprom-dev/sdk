<?php

class DictionaryItemForm extends PMPageForm
{
 	function extendModel()
 	{
 	 	if ( $this->getObject() instanceof Environment )
 		{
 			$this->getObject()->setAttributeVisible('OrderNum', false);
 			$this->getObject()->setAttributeVisible('IncidentsCount', false);
 			$this->getObject()->setAttributeVisible('Issues', false);
 			$this->getObject()->setAttributeVisible('RecentComment', false);
 		}
 		
 		parent::extendModel();
 	}
 	
 	function getFieldValue( $attr )
 	{
        $value = parent::getFieldValue( $attr );
 		switch($attr) {
 		    case 'HasIssues':
 		    	return $value == '' ? 'Y' : $value;
            case 'ReferenceName':
                if ( $value == '' ) {
                    return strtolower(get_class($this->getObject())) . $this->getObject()->getRecordCount();
                }
                return $value;
 		    default:
 		    	return $value;
 		}
 	}
}