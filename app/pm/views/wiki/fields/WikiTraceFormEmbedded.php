<?php

include_once SERVER_ROOT_PATH."pm/methods/ActuateWikiLinkWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/SyncWikiLinkWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/IgnoreWikiLinkWebMethod.php";
include_once "FieldWikiPageTrace.php";

class WikiTraceFormEmbedded extends PMFormEmbedded
{
 	var $trace_object;
 	
 	function setTraceObject ( $object )
 	{
 		$this->trace_object = $object;
 	}
 	
 	function IsAttributeVisible( $attribute )
 	{
 	    return in_array($attribute, array($this->getMenuField())); 
 	}
 	
	function getAttributeObject( $name )
	{
		global $model_factory;
		
		if ( $name == $this->getMenuField() ) 
		{
			return $this->trace_object;
		}
		
		return parent::getAttributeObject( $name );
	}
	
 	function drawFieldTitle( $attr )
 	{
 	}
	

 	function createField( $attr )
 	{
 		switch ( $attr )
 		{
 			case $this->getMenuField():

 			    $object = $this->getAttributeObject( $attr );

				$field = new FieldWikiPageTrace( $object );
				
				$field->setFormId( $this->getFormId() );
				
				$field->setTitle( $object->getDisplayName() ); 
				
				return $field;
				
 			default:
 				
 			    return parent::createField( $attr );
 		}
 	}
 	
 	function getMenuField()
 	{
 	    return 'TargetPage';
 	}
 	
 	function getActions( $object_it, $item )
 	{
 		global $model_factory;
 		
 		$actions = array();
 		
 		$page_it = $object_it->getRef($this->getMenuField());
 		
		array_push ( $actions, array( 
			'click' => "javascript: window.location = '".$page_it->getViewUrl()."&baseline=".$object_it->get('Baseline')."';",
			'name' => translate('Открыть') ) 
		);

		$baseline_it = $model_factory->getObject('Snapshot')->getRegistry()->Query( array (
				new SnapshotsByObjectPredicate( $object_it->getRef('SourcePage') )
		));
		
		if ( $object_it->get('IsActual') == 'N')
		{
			if ( $object_it->get('Type') == 'branch' )
			{
				$method = new SyncWikiLinkWebMethod($object_it);
				
				$method->setRedirectUrl("function() { $('#trace-state-".$object_it->getId()."').remove(); }");
				
				$actions[] = array();
				
				$actions[] = array( 
					'click' => $method->getJSCall(),
					'name' => $method->getCaption() 
				);
				
				$method = new IgnoreWikiLinkWebMethod($object_it);
						
				$method->setRedirectUrl("function() { $('#trace-state-".$object_it->getId()."').remove(); }");
				
				$actions[] = array( 
					'click' => $method->getJSCall(),
					'name' => $method->getCaption() 
				);
			}
			else
			{
				$method = new ActuateWikiLinkWebMethod($object_it);
				
				$method->setRedirectUrl("function() { $('#trace-state-".$object_it->getId()."').remove(); }");
				
				$actions[] = array();
				
				$actions[] = array( 
					'click' => $method->getJSCall(),
					'name' => $method->getCaption() 
				);
				
				$baseline_it->moveFirst();
				
				$separator = true;
				
				while( !$baseline_it->end() )
				{
					if ( $separator )
					{
						$actions[] = array();
						
						$separator = false;
					}
					
					$method = new ActuateWikiLinkWebMethod($object_it, $baseline_it);
							
					$actions[] = array( 
						'click' => $method->getJSCall(),
						'name' => $method->getCaption() 
					);
					
					$baseline_it->moveNext();
				}
			}
		}
		
		array_push ( $actions, array() );

		$history_url = $page_it->getHistoryUrl();
		
		$baseline_it = $object_it->getRef('Baseline');
		
		if ( $baseline_it->getId() > 0 )
		{
			$history_url .= '&start='.$baseline_it->getDateTimeFormat('RecordCreated');
		}
		else if ( $object_it->get('Type') == 'branch' )
		{
			$history_url .= '&start='.$object_it->getRef('TargetPage')->getDateTimeFormat('RecordCreated');
		}
		
		array_push ( $actions, array( 
			'click' => "javascript: window.location = '".$history_url."';", 
			'name' => translate('История изменений') )
		);
 		
		array_push ( $actions, array() );
		
		return array_merge($actions, parent::getActions( $object_it, $item ));
	}
}