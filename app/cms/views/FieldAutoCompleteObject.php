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
    private $multiple = false;
    private $objectIt = null;
 	
 	function __construct( $object, $attributes = null )
 	{
 	    $this->object = $object;
 	    
 		if ( is_array($attributes) ) {
 			$this->attributes = $attributes;
 		}
 		else {
 			$this->attributes = array( 'Caption' );
 		}

 		$this->additional_attributes = $this->object->getAttributesByGroup('search-attributes');
 		if ( !defined('PERMISSIONS_ENABLED') ) {
 		    $this->setCrossProject();
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

 	function setMultiple( $value = true ) {
 	    $this->multiple = $value;
    }
 	
 	function setDefault( $value )
 	{
		if ( $value != '' ) {
			$this->setValue( $value );
		}
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

	function setCrossProject()
	{
		$this->attributes[] = 'cross';
	}

	function setSystemAttribute($attribute) {
        $this->attributes[] = $attribute;
    }

	function removeCrossProject() {
 	    $index = array_search('cross', $this->attributes);
 	    if ( $index ) unset($this->attributes[$index]);
    }

	function getCrossProject()
	{
		return in_array('cross', $this->attributes);
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
 	    if ( is_object($this->objectIt) ) {
 	        return $this->objectIt->copyAll();
        }
 	    return $this->objectIt = $this->buildObjectIt();
    }

    function buildObjectIt()
 	{
 	    $value = $this->getValue();
 	    if ( $value == '' ) return $this->getObject()->createCachedIterator(array());

		$registry = $this->getObject()->getRegistry();
 	    if ( $this->search_enabled )
 	    {
	 	    $ids = \TextUtils::parseIds($value);
	 	    if ( count($ids) > 0 ) {
                if ( $this->getObject() instanceof MetaobjectCacheable ) {
                    return $this->getObject()->getExact($ids);
                }
	 	    	return $registry->Query(
					array (
						new FilterInPredicate($ids)
					)
				);
	 	    }
	 	    else {
				$object_it = $this->getObject()->getExact($value);
				if ( $object_it->getId() != '' ) return $object_it;

                $registry->setLimit(1);
		 	    return $registry->Query(
					array (
						new FilterTextExactPredicate('Caption', $value)
					)
		 	    );
	 	    }
 	    }
 	    else
 	    {
 	        if ( $this->getObject() instanceof MetaobjectCacheable ) {
                return $this->getObject()->getExact($value);
            }
 	    	return $registry->Query(
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
		$titles = array();
		while( !$object_it->end() ) {
			$titles[] = $uid->getUidWithCaption($object_it);
			$object_it->moveNext();
		}
		return join('<br/>', $titles);
	}
 	
 	function draw( $view = null )
 	{
 		$object = $this->getObject();
		$project = getSession()->getProjectIt()->get('CodeName');
 		$object_it = $this->getObjectIt();

		if ( $this->readOnly() )
		{
		    $this->drawReadonly($view);
		}
		else
		{
			$text = $this->getTitle() != '' ? $this->getTitle() : text(868);
			$displayValue = TextUtils::stripAnyTags($object_it->getDisplayName());

            echo '<input type="text" class="autocomplete-text input-block-level" id="'.$this->getName().'Text" auto-expand="'.($this->auto_expand?'true':'false').'" tabindex="'.$this->getTabIndex().'" style="'.$this->getStyle().'" placeholder="'.text(1338).'" default="'.$displayValue.'" value="'.$displayValue.'" '.($this->getRequired() ? 'required' : '').' multiple="'.var_export($this->multiple, true).'">';
            echo '<input class="fieldautocompleteobject" type="hidden" name="'.$this->getName().'" id="'.$this->getId().'" default="'.$this->getDefault().'" value="'.$object_it->getId().'" object="'.get_class($object).'" caption="'.$text.'" searchattrs="'.join(',', $this->getAttributes()).'" additional="'.join(',', $this->getAdditionalAttributes()).'" '.($this->getRequired() ? 'required' : '').' ondblclick="javascript: '.$this->getOnSelectCallback().';" project="'.$project.'">';
		}
 	}

 	function drawReadonly($view)
    {
        echo '<div id="'.$this->getId().'" class="autocomplete">';
            if ( $this->custom_text != '' )
            {
                echo $this->custom_text;
            }
            else
            {
                $object_it = $this->getObjectIt();
                if ( $object_it->getId() != '' ) {
                    $uid = new ObjectUID;
                    echo $uid->getUidWithCaption($object_it, 50);
                }
                else
                {
                    echo $this->getText();
                }
            }
        echo '</div>';
        echo '<input type="hidden" name="'.$this->getName().'" value="'.$this->getEncodedValue().'">';
    }
}
