<?php
include_once SERVER_ROOT_PATH."pm/methods/OpenBrokenTraceWebMethod.php";
include_once SERVER_ROOT_PATH."pm/views/wiki/fields/FieldWikiPageTrace.php";

class ObjectTraceFormEmbedded extends PMFormEmbedded
{
 	var $trace_object;
 	private $trace_type = '';
 	private $trace_field_name = '';
 	private $createParameters = array();

 	function setCreateParameters( $parms ) {
 	    $this->createParameters = $parms;
    }

 	function setTraceObject ( $object )
 	{
 		$this->trace_object = $object;
 	}
 	
 	public function setTraceType( $type )
 	{
 		$this->trace_type = $type;
 	}
 	
 	public function setTraceFieldName( $field )
 	{
 		$this->trace_field_name = $field;
 	}
 	
 	public function getTraceFieldName()
 	{
 		return $this->trace_field_name;
 	} 	
 	
 	public function getTraceType()
 	{
 		return $this->trace_type;
 	}
 	
 	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'ObjectId':
 				return true;
 			
 			case 'ObjectClass':
 			case 'ChangeRequest':
 			case 'IsActual':
 			case 'RecordModified':
 			case 'RecordCreated':
 			case 'OrderNum':
 			case 'Task':
 			case 'ChangeRequest':
 			case 'Feature':
 				return false;
 				
 			default:
 				return parent::IsAttributeVisible( $attribute );
 		}
 	}
 	
 	function getFieldValue( $name )
 	{
 		switch ( $name )
 		{
 		    case 'Type':
 		    	return $this->getTraceType();
 		    	
 		    default:
 		    	return parent::getFieldValue( $name );
 		}
 	}
 	
	function getAttributeObject( $name )
	{
		if ( $name == 'ObjectId' && !$this->getObject()->IsReference($name) ) {
			return $this->trace_object;
		}
		
		return parent::getAttributeObject( $name );
	}
	
 	function drawFieldTitle( $attr )
 	{
 		if ( is_a($this->trace_object, 'Milestone') )
 		{
 			return parent::drawFieldTitle( $attr );
 		}
 	}
 	
 	function createField( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'ObjectId':
 			    $object = $this->getAttributeObject( $attr );
 			    if ( $object instanceof WikiPage ) {
 			    	$field = new FieldWikiPageTrace($object, $this->getFormId());
                    $field->setBaselineAttribute($this->getObject()->getBaselineReference());
                    $field->setCreateParameters($this->createParameters);
 			    }
 			    else {
					$field = new FieldAutoCompleteObject($object);
 			    }
				$field->setTitle( $object->getDisplayName() );
				return $field;
				
 			default:
 			    return parent::createField( $attr );
 		}
 	}
 	
 	function getTargetIt( $object_it )
 	{
 	    return $object_it->getObjectIt();
 	}
 	
 	function getActions( $object_it, $item )
 	{
		$actions = array();
		$baselineAttribute = $this->getObject()->getBaselineReference();
 	    $anchor_it = $this->getTargetIt($object_it);

        $url = $anchor_it->getUidUrl();
        if ( $object_it->get($baselineAttribute) != '' ) {
            $url .= strpos($url, '?') >= 0
                ? '&baseline='.$object_it->get($baselineAttribute)
                : '?baseline='.$object_it->get($baselineAttribute);
        }
        $actions[] = array (
            'name' => translate('Открыть'),
            'url' => $url,
            'uid' => 'open-form'
        );

        if ( !$anchor_it->object instanceof TestCaseExecution && $_REQUEST['formonly'] == '' )
        {
            if ( $anchor_it->object instanceof Commit ) {
                $plugin_actions = array();
                foreach( PluginsFactory::Instance()->getPluginsForSection(getSession()->getSite()) as $plugin ) {
                    $plugin_actions = array_merge($plugin_actions, $plugin->getObjectActions( $anchor_it ));
                }
                if ( count($plugin_actions) > 0 ) {
                    $actions = array_merge( $actions, array(array()), $plugin_actions );
                }
            }
        }

        if ( $anchor_it->object instanceof WikiPage )
        {
            $actions[] = array();
            if ( $anchor_it->get('Suspected') > 0 ) {
                $method = new OpenBrokenTraceWebMethod();
                $actions[] = array(
                    'name' => text(1933),
                    'url' => $method->getJSCall(array('object' => $anchor_it->getId()))
                );
            }
            else {
                $actions[] = array(
                    'url' => $anchor_it->getHistoryUrl(),
                    'target' => "_blank",
                    'name' => text(1933)
                );
            }
            $actions[] = array();
        }

 	    return array_merge($actions, parent::getActions( $object_it, $item ));
 	}

	function getListItemsAttribute() {
		return 'ObjectId';
	}

    function drawAddButton($view, $tabindex)
    {
        parent::drawAddButton($view, $tabindex);

        $value = $_REQUEST[$this->getFormField()];
        if ( $value != '' || $_REQUEST['Request'] != '' ) {
            switch( $this->getFormField() ) {
                case 'Issues':
                    $object_it = getFactory()->getObject('Issue')->getExact($_REQUEST['Request']);
                    break;
                case 'Increments':
                    $object_it = getFactory()->getObject('Increment')->getExact($_REQUEST['Request']);
                    break;
                default:
                    $object_it = $this->trace_object->getExact($value);
            }
            if ( $object_it->getId() == '' ) return;
            $uid = new ObjectUID();
            echo '<br/>';
            echo '<br/>';
            $uid->drawUidInCaption($object_it);
        }
    }
}