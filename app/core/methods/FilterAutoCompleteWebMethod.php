<?php

include_once "AutocompleteWebMethod.php";

class FilterAutoCompleteWebMethod extends AutocompleteWebMethod
{
	private $idfieldname = '';
	
 	function FilterAutoCompleteWebMethod( $object = null, $title = '', $value_parm = '' )
 	{
 		parent::__construct( $object, $title );

 		if ( is_object($object) )
 		{
	 		$this->setValueParm($value_parm != '' ? $value_parm : strtolower(get_class($object)));
	 		
	 		$this->idfieldname = $object->getEntityRefName().'Id';
 		}
 	}
 	
 	function getStyle()
 	{
 		return 'width:220px;';
 	}

 	function setIdFieldName( $field )
 	{
 		$this->idfieldname = $field;
 	}
 	
 	function drawSelect( $parms_array = array() ) 
 	{
 		global $script_number;
 		$script_number += 1;
 		
 		$parms_array['class'] = get_class($this->object);
 		$url = $this->getUrl( $parms_array );

 		$default_value = $this->getValue();
 		
 		if ( $default_value != '' && $default_value != 'all' )
 		{
 			$uid = new ObjectUID;
 			
 			$search_by_id = $this->idfieldname == $this->object->getEntityRefName().'Id';
 			
 			$value_it = $this->object->getRegistry()->Query(
 					array (
		 					 $search_by_id && is_numeric($default_value)
		 						? new FilterInPredicate($default_value)
		 						: new FilterAttributePredicate($search_by_id ? 'Caption' : $this->idfieldname, $default_value)
 					)
 			);

 			$value = $uid->hasUid($value_it)
 				? '['.$uid->getObjectUid($value_it).'] '.$value_it->getDisplayName()
 				: $value_it->getDisplayName();
 		}

	 	echo '<input type="text" class="btn-small input-xxlarge" placeholder="'.$this->getCaption().'" id="filter_'.$this->getValueParm().'" style="'.
	 		$this->getStyle().';'.($default_value != '' ? '' : '').'" value="'.$value.'" title="'.$this->getTitle().'">';
	 		
	 	echo '<script type="text/javascript">$(document).ready(function(){ filterAutoComplete("'.
	 		$this->getValueParm().'", "'.$url.'", "'.$this->title.'"); });</script>';
 	}

	function getCaption()
	{
	    return parent::getTitle();    
	}
	
	function getTitle()
	{
		$title = parent::getTitle();
		
		if ( $title != '' ) $title .= '. ';
		
	    return $title.text(1293);
	}
}