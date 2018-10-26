<?php

class FeatureIterator extends OrderedIterator
{
	function getDisplayName()
    {
        $title = $this->get('CaptionAndType') != '' ? $this->get('CaptionAndType') : parent::getDisplayName();

        if ( $this->get('TagNames') != '' ) {
            $tags = array_map(function($value) {
                return ' <span class="label label-info label-tag">'.$value.'</span> ';
            }, preg_split('/,/', $this->get('TagNames')));
            $title .= join('',$tags);
        }

        return $title;
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

        if ( $this->get('RootCaption') != '' ) {
            $prefix .= $this->getHtmlDecoded('RootCaption') . ' / ';
        }

        $title .= parent::getDisplayNameExt($prefix);
        return $title;
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