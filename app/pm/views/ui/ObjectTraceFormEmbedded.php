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
		if ( $name == 'ObjectId' ) {
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
 	    $anchor_it = $this->getTargetIt($object_it);

        if ( !$anchor_it->object instanceof TestCaseExecution && $_REQUEST['formonly'] == '' )
        {
            $method = new ObjectModifyWebMethod($anchor_it);
            $actions[] = array (
                'name' => translate('Редактировать'),
                'url' => $method->getJSCall(),
                'uid' => 'open-form'
            );

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

		$url = $anchor_it->getViewUrl();
		if ( $object_it->get('Baseline') != '' ) {
			$url .= strpos($url, '?') >= 0
					? '&baseline='.$object_it->get('Baseline')
					: '?baseline='.$object_it->get('Baseline');
		}
		if ( $object_it->get('Revision') != '' ) {
            $url .= strpos($url, '?') >= 0 ? '&' : '?';
            if ( $object_it->get('Baseline') == '' ) {
                $changeIt = $object_it->getRef('Revision');
                $url .= '&bydate='.$changeIt->getDateFormat('RecordCreated');
            }
        }

        if ( $anchor_it->object instanceof WikiPage )
        {
            $actions[] = array();
            if ( $anchor_it->get('BrokenTraces') != "" ) {
                $method = new OpenBrokenTraceWebMethod();
                $actions[] = array(
                    'name' => text(1933),
                    'target' => "_blank",
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
        }

        $actions[] = array();
 	    $actions[] = array (
			'name' => $anchor_it->object instanceof WikiPage ? text(2163) : translate('Открыть'),
			'url' => $url,
			'uid' => 'show-in-document',
            'target' => defined('SKIP_TARGET_BLANK') && SKIP_TARGET_BLANK ? '' : '_blank'
		);
		$actions[] = array();

 	    return array_merge($actions, parent::getActions( $object_it, $item ));
 	}

	function getListItemsAttribute() {
		return 'ObjectId';
	}

    function drawAddButton($view, $tabindex)
    {
        parent::drawAddButton($view, $tabindex);

        $field = $this->getFormField();
        if ( $field == 'Issues' ) $field = 'Request';

        $value = $_REQUEST[$field];
        if ( $value != '' ) {
            $uid = new ObjectUID();
            echo '<br/>';
            echo '<br/>';
            if ( $field == 'Request' ) {
                $object_it = getFactory()->getObject('Request')->getExact($value);
            }
            else {
                $object_it = $this->trace_object->getExact($value);
            }
            $uid->drawUidInCaption($object_it);
        }
    }

    function getFieldDescription($attr)
    {
        $value = parent::getFieldDescription($attr);
        if ( $this->trace_object instanceof Milestone ) {
            if ( $attr == 'ObjectId' ) {
                $value = str_replace('%1', getFactory()->getObject('Module')->getExact('project-plan-hierarchy')->getUrl(), text(2275));
            }
        }
        return $value;
    }
}