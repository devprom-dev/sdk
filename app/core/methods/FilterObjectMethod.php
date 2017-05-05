<?php

include_once "FilterWebMethod.php";

class FilterObjectMethod extends FilterWebMethod
{
 	var $object;
 	var $has_all;
 	var $parmvalue;
 	var $it = null;
 	var $idfieldname;
 	var $none_title;
 	var $use_uid;
 	var $has_none;
 	private $rowsVisibilityLimit = 15;

 	function FilterObjectMethod( $object = null, $title = '', $parmvalue = '', $has_all = true )
 	{
 		global $_REQUEST;
 		
 		parent::FilterWebMethod();

 		if ( is_object($object) )
 		{
 			if ( is_a($object, 'Metaobject') ) 
 			{
		 		$this->object = $object;
 			}
 			else 
 			{
		 		$this->it = $object;
 				$this->object = $object->object;
 			}
 		}
 		
 		$this->has_all = $has_all;
 		$this->has_none = true;
 		$this->parmvalue = $parmvalue;
 		$this->use_uid = false;
 		
 		if ( is_object($this->object) )
	 		$this->idfieldname = $this->object->getClassName().'Id'; 
 		
 		if ( is_object($this->object) )
 		{
	 		$this->title = $title != '' 
	 			? $title : translate($this->object->getDisplayName());
	 			
	 		if ( $this->parmvalue == '' )
				$this->parmvalue = strtolower(get_class($this->object));
 		}

 		if ( $this->parmvalue == '' ) $this->parmvalue = $_REQUEST['object'];
 		
 		$this->none_title = translate('<нет значения>');
	}
 	
 	function getModule() 
 	{
 		return '';
 	}

 	function setIdFieldName( $field )
 	{
 		$this->idfieldname = $field;
 	}
 	
 	function setNoneTitle( $title )
 	{
 	    $this->none_title = $title;
 	}
 	
  	function setHasAll( $has_all )
 	{
 	    $this->has_all = $has_all;
 	}
 	
 	function setHasNone( $has_none )
 	{
 	    $this->has_none = $has_none;
 	}
 	
 	function setUseUid( $use_uid )
 	{
 	    $this->use_uid = $use_uid;
 	}
 	
 	function getStyle()
 	{
 		return 'width:180px;';
 	}

	function getCaption()
	{
		return $this->title;
	}

	function getValues()
	{
		$values = array();
		
		$uid = new ObjectUID;
		
		if ( !is_object($this->it) )
		{
			$registry = $this->object->getRegistryDefault();
			$registry->setPersisters(array(
                new EntityProjectPersister()
            ));
            $registry->setSorts(array());
	 		$this->it = $registry->getAll();
		}

		$selected_values = preg_split('/,/', $this->getValue());
		$selected_value_found = false;

		while ( !$this->it->end() )
		{
			$display_name = $this->use_uid ? $uid->getUidTitle($this->it) : ' '.$this->it->getDisplayName();
			$item_value = $this->it->get($this->idfieldname);

			if ( $item_value == '' ) 
			{
				$this->it->moveNext();
				continue;
			}
			
			if ( !isset($values[$display_name]) )
			{
				$values[$display_name] = array($item_value);
			}
			elseif ( !in_array($item_value, $values[$display_name]) )
			{
				$values[$display_name][] = $item_value;
			}
				
			if ( in_array($item_value, $selected_values) ) $selected_value_found = true;
			
			$this->it->moveNext();
		}

		array_walk( $values, function(&$value) {
				$value = is_array($value) ? ' '.join(',', $value) : '';
		});

		$values = array_flip($values);
		$itemsCount = count($values);

		array_walk( $values, function(&$value) {
				$value = trim($value);
		});

		if ( $this->has_none ) {
			$values = array_merge( array ( 'none' => $this->none_title ), $values );
		}
		if ( $this->has_all ) {
			$values = array_merge( array ( 'all' => translate('Все') ), $values );
		}
		if ( $itemsCount > $this->rowsVisibilityLimit ) {
			$values = array_merge( array ( 'search' => array( 'uid' => 'search') ), $values );
		}

 		if ( $selected_value_found || count(array_intersect($selected_values, array('', 'all', 'none'))) > 0 ) return $values;
 		
		$object_it = $this->object->getRegistry()->Query( 
				array (
						$this->it->getIdAttribute() == $this->idfieldname
							? new FilterInPredicate($selected_values)
							: new FilterAttributePredicate($this->idfieldname, $selected_values),
						new FilterVpdPredicate()
				)
		);
		while ( !$object_it->end() ) {
			$values[' '.$object_it->get($this->idfieldname)] = $uid->getUidTitle($object_it);
			$object_it->moveNext();
		}

		return $values;
	}
	
	function getValueParm()
	{
		return $this->parmvalue;
	}

 	function drawSelect( $parms_array = array() ) 
 	{
 		SelectRefreshWebMethod::drawSelect( 
 			array('setting' => $this->method_name,
 				  'object' => $this->getValueParm() ), 
 			$this->getValue() 
 		);
 	}
}