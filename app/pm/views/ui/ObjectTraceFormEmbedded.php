<?php

include_once SERVER_ROOT_PATH."pm/views/wiki/fields/FieldWikiPageTrace.php";

class ObjectTraceFormEmbedded extends PMFormEmbedded
{
 	var $trace_object;
 	
 	private $trace_type = '';
 	
 	private $trace_field_name = '';
 	
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
 			    }
 			    else {
					$field = new FieldAutoCompleteObject($object);
					$field->setAdditionalAttributes($object->getAttributesByGroup('search'));
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

		$method = new ObjectModifyWebMethod($anchor_it);
		$actions[] = array (
			'name' => translate('Открыть'),
			'url' => $method->getJSCall(),
			'uid' => 'open-form'
		);

		if ( $anchor_it->object instanceof SubversionRevision ) {
			$plugin_actions = array();
			foreach( PluginsFactory::Instance()->getPluginsForSection(getSession()->getSite()) as $plugin ) {
				$plugin_actions = array_merge($plugin_actions, $plugin->getObjectActions( $anchor_it ));
			}
			if ( count($plugin_actions) > 0 ) {
				$actions = array_merge( $actions, array(array()), $plugin_actions );
			}
		}

		$url = $anchor_it->getViewUrl();
		if ( $object_it->get('Baseline') != '' ) {
			$url .= strpos($url, '?') >= 0
					? '&baseline='.$object_it->get('Baseline')
					: '?baseline='.$object_it->get('Baseline');
		}
		$actions[] = array();
 	    $actions[] = array (
			'name' => $anchor_it->object instanceof WikiPage ? text(2163) : translate('Перейти'),
			'url' => $url,
			'uid' => 'show-in-document'
		);
		$actions[] = array();

 	    return array_merge($actions, parent::getActions( $object_it, $item ));
 	}

	function getListItemsAttribute() {
		return 'ObjectId';
	}
}