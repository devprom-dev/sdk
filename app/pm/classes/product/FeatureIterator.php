<?php

class FeatureIterator extends OrderedIterator
{
	function getDisplayName() {
		return $this->get('CaptionAndType') != '' ? $this->get('CaptionAndType') : parent::getDisplayName();
	}

	function getDisplayNameExt($prefix = '')
    {
        $title = '';
        if ( $this->get('ImportanceName') != '' ) {
            if ( strpos($this->get('ImportanceColor'),'#') !== false ) {
                $title = '<span class="label label-uid" style="background:'.$this->get('ImportanceColor').';'.ColorUtils::getTextStyle($this->get('ImportanceColor')).'">'.$this->get('ImportanceName').'</span> ';
            }
            else {
                $title = '<span class="label label-warning">'.$this->get('ImportanceName').'</span> ';
            }
        }
        return $title.parent::getDisplayNameExt($prefix);
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