<?php

include_once "Field.php";

class FieldAutoCompleteObject extends Field
{
 	var $attributes;
 	var $title;
 	var $object;
 	private $additional_attributes = array();
 	private $select_event = '';
 	private $auto_expand = true;
 	private $custom_text = '';
 	private $search_enabled = true;
 	
 	function __construct( $object, $attributes = null )
 	{
 	    $this->object = $object;
 	    
 		if ( is_array($attributes) )
 		{
 			$this->attributes = $attributes;
 		}
 		else
 		{
 			$this->attributes = array( 'Caption' );
 		}
 		
 		parent::__construct();
 	}
 	
 	function getObject()
 	{
 	    return $this->object;
 	}
 	
 	function getStyle()
	{
		return '';
	}
 	
 	function setTitle( $title )
 	{
 		$this->title = $title;
 	}
 	
 	function setDefault( $value )
 	{
 		$this->setValue( $value );
 		
 		parent::setDefault( $value );
 	}
 	
 	function setSearchEnabled( $flag = true )
 	{
 		$this->search_enabled = $flag;
 	}
 	
 	function getTitle()
 	{
 		return $this->title;
 	}
 	
 	function setAppendable()
 	{
 		$this->attributes[] = 'itself';
 	}
 	
 	function getAppendable()
 	{
 		return in_array('itself', $this->attributes);
 	}
 	
 	function getAttributes()
 	{
 		return $this->attributes;
 	}
 	
 	public function setAdditionalAttributes( $attributes )
 	{
 		$this->additional_attributes = $attributes;
 	}
 	
 	public function getAdditionalAttributes()
 	{
 		return $this->additional_attributes;
 	}
 	
 	public function setOnSelectCallback( $script )
 	{
 		$this->select_event = $script;
 	}
 	
 	public function getOnSelectCallback()
 	{
 		return $this->select_event;
 	}
 	
 	public function setAutoExpand( $flag )
 	{
 		$this->auto_expand = $flag;
 	}
 	
 	function readOnly()
 	{
 	    return !$this->getEditMode() || parent::readOnly();
 	}

 	function getObjectIt()
 	{
 	    $value = $this->getValue();

 	    if ( $value == '' ) return $this->getObject()->createCachedIterator(array());
 	    
 	    if ( $this->search_enabled )
 	    {
	 	    $ids = array_filter(preg_split('/[,-]/',$value), function($id) {
	 	    			return is_numeric($id);
	 	    		});
	
	 	    if (  count($ids) > 0 )
	 	    {
	 	    	return $this->getObject()->getExact($ids);
	 	    }
	 	    else
	 	    {
		 	    return $this->getObject()->getRegistry()->Query(
		 	    		array (
	    						new FilterAttributePredicate('Caption', $value)
		 	    		)
		 	    );
	 	    }
 	    }
 	    else
 	    {
 	    	return $this->getObject()->getRegistry()->Query(
		 	    		array (
	    						new FilterInPredicate($value)
		 	    		)
		 	    );
 	    }
 	}
 	
 	function setText( $text )
 	{
 		$this->custom_text = $text;
 	}
 	
	function getText()
	{
		if ( $this->custom_text != '' ) return $this->custom_text;
		
		$object_it = $this->getObjectIt();
		
		if ( $object_it->getId() == '' ) return $this->getValue();
		
	    $uid = new ObjectUID;
	    
		return $uid->getUidTitle($object_it);
	}
 	
 	function draw()
 	{
 		global $model_factory;

 		$object = $this->getObject();
		
 		$object_it = $this->getObjectIt();

		if ( $this->readOnly() )
		{
			echo '<div id="'.$this->getId().'" class="input-block-level well well-text">';

				if ( $this->custom_text != '' )
				{
					echo $this->custom_text;
				}
				else
				{
					$object_it = $this->getObjectIt();

					if ( $object_it->getId() != '' )
					{
					    $uid = new ObjectUID;
					    
						echo $uid->getUidWithCaption($object_it);
					}
					else
					{
						echo $this->getText();
					}
				}
				
			echo '</div>';
			
			echo '<input type="hidden" name="'.$this->getName().'" value="'.$this->getEncodedValue().'">';
			
			return;
		}
		else
		{
			$text = $this->getTitle() != '' ? $this->getTitle() : text(868);
			
			echo '<div class="autocomplete">';
			 	echo '<input type="text" class="autocomplete-text input-block-level" id="'.$this->getName().'Text" auto-expand="'.($this->auto_expand?'true':'false').'" tabindex="'.$this->getTabIndex().'" style="'.$this->getStyle().'" placeholder="'.text(1338).'" value="'.$object_it->getDisplayName().'" '.($this->getRequired() ? 'required' : '').' >';
			 	echo '<input class="fieldautocompleteobject" type="hidden" name="'.$this->getName().'" id="'.$this->getId().'" default="'.$this->getDefault().'" value="'.$this->getEncodedValue().'" object="'.get_class($object).'" caption="'.$text.'" searchattrs="'.join(',', $this->getAttributes()).'" additional="'.join(',', $this->getAdditionalAttributes()).'" '.($this->getRequired() ? 'required' : '').' ondblclick="javascript: '.$this->getOnSelectCallback().';" >';
			echo '</div>';
		}
 	}
}
