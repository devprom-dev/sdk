<?php
include_once SERVER_ROOT_PATH."pm/methods/ActuateWikiLinkWebMethod.php";
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
		if ( $name == $this->getMenuField() ) {
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
				$field = new FieldWikiPageTrace($object, $this->getFormId());
				$field->setTitle( $object->getDisplayName() );
				$field->setBaselineAttribute($this->getObject()->getBaselineReference());
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
 		$actions = array();
 		
 		$page_it = $object_it->getRef($this->getMenuField());
 		
		array_push ( $actions, array( 
			'click' => "javascript: window.location = '".$page_it->getUidUrl()."&baseline=".$object_it->get($object_it->object->getBaselineReference())."';",
			'name' => translate('Открыть') ) 
		);

		array_push ( $actions, array() );

		$history_url = $page_it->getHistoryUrl();
		
		$baseline_it = $object_it->getRef($object_it->object->getBaselineReference());
		
		if ( $baseline_it->getId() > 0 )
		{
			$history_url .= '&start='.$baseline_it->getDateTimeFormat('RecordCreated');
		}
		else if ( $object_it->get('Type') == 'branch' )
		{
			$history_url .= '&start='.$object_it->getRef('TargetPage')->getDateTimeFormat('RecordCreated');
		}
		
		$actions[] = array(
			'click' => "javascript: window.location = '".$history_url."';", 
			'name' => text(824)
		);

        $versions_url = $page_it->getPageVersions();
        if ( $versions_url != '' ) {
            $actions[] = array(
                'url' => $versions_url,
                'target' => "_blank",
                'name' => text(2237)
            );
        }
 		
		array_push ( $actions, array() );
		
		return array_merge($actions, parent::getActions( $object_it, $item ));
	}

	function getListItemsAttribute() {
		return $this->getMenuField();
	}

	function drawAddButton($view, $tabindex)
    {
        parent::drawAddButton($view, $tabindex);

        $value = $_REQUEST[$this->getFormField()];
        if ( $value != '' ) {
            $uid = new ObjectUID();
            echo '<br/>';
            echo '<br/>';
            $uid->drawUidInCaption($this->trace_object->getExact($value));
        }
    }
}