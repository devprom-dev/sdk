<?php

class FeatureIterator extends OrderedIterator
{
	function getDisplayName()
	{
		return $this->get('CaptionAndType') != '' ? $this->get('CaptionAndType') : parent::getDisplayName();
	}
	
	function getParentsArray()
	{
		return array_filter(preg_split('/,/',$this->get('ParentPath')), function($value) {
					return is_numeric($value);
		});		
	}
	
 	function getTransitiveRootArray()
	{
	    $roots = array();
	    
		$parent_page = $this->getId();
		
		while( $parent_page != '' ) 
		{
		    $roots[] = $parent_page;
		    
			$parent_page_it = $this->object->getExact($parent_page);
			
			if( $parent_page_it->get('ParentFeature') == '' ) break; 
			
			$parent_page = $parent_page_it->get("ParentFeature");
		}
		
		return $roots;
	}
} 