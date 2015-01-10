<?php

include (dirname(__FILE__).'/../methods/c_project_methods.php');

//////////////////////////////////////////////////////////////////////////////////////////////
class ProjectList extends PageList
{
	function setupColumns()
	{
        $this->object->setAttributeCaption( 'IsClosed', translate('Состояние') ); 
 		
 		$this->object->addAttribute('Coordinators', 'REF_cms_UserId', translate('Координаторы'), true);
		
		$this->object->addPersister( new ProjectLeadsPersister() );
	    
        parent::setupColumns();
		
        $visible = array( 'Caption', 'CodeName', 'Coordinators', 'IsClosed' );

	    $attrs = array_keys($this->object->getAttributes());
        
        foreach( $attrs as $attribute )
        {
            if ( in_array($attribute, $visible) ) continue;
            
            $this->object->setAttributeVisible($attribute, false);
        }
		
		$this->object->setAttributeVisible('UID', true);
	}

	function IsNeedToModify( $object_it )
	{ 
	    return false; 
	}

	function getItemActions( $column_name, $object_it )
	{
		global $_SERVER;

		$actions = parent::getItemActions( $column_name, $object_it );

		$method = new ModifyAttributeWebMethod($object_it, 'IsClosed', $object_it->get('IsClosed') == 'Y' ? 'N' : 'Y');
		
		if ( $method->HasAccess() )
		{
			$method->setRedirectUrl('donothing');
			
		    if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
		    
		    $actions[] = array(
    		    'url' => $method->getJSCall(),
		    	'name' => $object_it->get('IsClosed') == 'Y' ? text(1320) : text(1319)
		    );
		}
		
		$method = new ProjectDeleteWebMethod($object_it);
		
		if ( $method->HasAccess() )
		{
		    if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
		    
			$actions[] = array( 
			    'url' => $method->getRedirectUrl(), 'name' => $method->getCaption() 
			);
		}

		return $actions;
	}

	function drawCell( $object_it, $attr )
	{
		global $model_factory;

		switch( $attr )
		{
		    case 'IsClosed':
		        
		        echo $object_it->get('IsClosed') == 'Y' ? translate('Закрыт') : translate('Открыт'); 
		        
		        break;
		        
		    default:
		        
		        parent::drawCell( $object_it, $attr );
		}
	}

	function getGroupDefault()
	{
		return '';
	}
}

