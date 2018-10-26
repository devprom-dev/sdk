<?php



class WrtfCKEditorChangeNotificator extends SystemTriggersBase
{
    function process( $object_it, $kind, $content = array(), $visibility = 1 )
	{
	    global $model_factory;

	    if ( $kind == TRIGGER_ACTION_DELETE ) return;

	    if ( $kind == TRIGGER_ACTION_MODIFY && !array_key_exists('Content', $content) ) return;
	    
		$entity_ref_name = $object_it->object->getEntityRefName();

		switch( $entity_ref_name )
		{
		    case 'WikiPage':
		        
		        $this->storeImages($object_it, 'Content');
		        
		        break;
		        
		    case 'BlogPost':
		        
		        $this->storeImages($object_it, 'Content');
		        
		        break;
		    
		    case 'WikiPageFile':
		        
		        $this->storeImages($object_it->getRef('WikiPage'), 'Content');
		        
		        break;
		        
		    case 'BlogPostFile':
		        
		        $this->storeImages($object_it->getRef('BlogPost'), 'Content');
		        
		        break;
		        
		    case 'pm_Attachment':
		        
		        $anchor_it = $object_it->getAnchorIt();
		        
		        switch ( $anchor_it->object->getEntityRefName() )
		        {
		            case 'Comment':
		                
		                $this->storeImages($anchor_it, 'Caption');
		                
		                break;
		                
		            default:
		                
                        foreach( $this->getWYSIWYGAttributes($anchor_it) as $attribute )
                        {
                        	$this->storeImages($anchor_it, $attribute);
                        }
		        }
		        
		        break;
		        
		    default:
		        
		        if ( !$object_it->object->attributesHasOrigin(ORIGIN_CUSTOM) ) break;

		        // proceed with objects who have custom attributes (wysiwyg is one of custom)
		        
		        $attributes = $this->getWYSIWYGAttributes($object_it);

                if ( count($attributes) > 0 )
                {
                    foreach( $attributes as $attribute )
                    {
                        $this->storeImages($object_it, $attribute);
                    }
                }
		}
	}
	
	function getWYSIWYGAttributes( $object_it )
	{
        $it = getFactory()->getObject('pm_CustomAttribute')->getByEntity($object_it->object);

        $wysiwyg_attributes = array();
        
        while (!$it->end()) 
        {
            if ( $it->getRef('AttributeType')->get('ReferenceName') == 'wysiwyg' ) 
            {
                $wysiwyg_attributes[] = $it->get('ReferenceName');
            }
            
            $it->moveNext();
        }
        
        foreach( $object_it->object->getAttributes() as $attribute => $data )
        {
            if ( $object_it->object->getAttributeType($attribute) == 'wysiwyg' ) $wysiwyg_attributes[] = $attribute;
        }
        
        return $wysiwyg_attributes;
	}
	
	function storeImages( $object_it, $field )
	{
		if ( $object_it->getId() < 1 ) throw new Exception('Unable store image on empty object'); 
		
		$object = $object_it->object;
		
		$this->object_it = $object->getRegistryBase()->Query( array (
				new FilterInPredicate($object_it->getId())
		));
		
		$object->setVpdContext( $object_it );
	    
		$field_value = $this->object_it->getHtmlDecoded($field);

		$result = preg_replace_callback( '/<img\s+([^>]*)>/i', array($this, 'replaceImageCallback'), $field_value);

		if ( $field_value != $result )
		{
			$object->getRegistryBase()->Store( $this->object_it, array ( $field => $result ) );
		}
	}
	
    function replaceImageCallback( $match )
    {
     	global $model_factory;
    
     	$attributes = $match[1];
     	
     	if ( preg_match( '/name="([^"]+)"/i', $attributes, $attrs ) )
     		$name = $attrs[1];
    
     	if ( preg_match( '/alt="([^"]+)"/i', $attributes, $attrs ) )
     		$name = $attrs[1];
     		
     	if ( trim($name) == "" )
     	{
     		// conversion is not required
     		return $match[0];
     	}
     	
     	if ( preg_match( '/src="([^"]+)"/i', $attributes, $attrs ) )
     		$url = $attrs[1];
    
     	$result = array();
     	
     	if ( preg_match_all('/cms_TempFile|WikiPageFile|BlogPostFile/i', $url, $result) )
     	{
     		$predicates = array();
     		
     		switch ( $this->object_it->object->getClassName() )
     		{
     			case 'WikiPage':
     				
     			    $file = $model_factory->getObject('WikiPageFile');
     				
     			    $predicates[] = new FilterAttributePredicate('WikiPage', $this->object_it->getId());
     				
     			    $key_field = 'ContentExt';
     				
     			    break;
     				
     			case 'BlogPost':
     				
     			    $file = $model_factory->getObject('BlogPostFile');
     				
     			    $predicates[] = new BlogPostFilePostFilter($this->object_it->getId());
     				
     			    $key_field = 'ContentExt';
     				
     			    break;
    
     			default:
     				
     			    $file = $model_factory->getObject('pm_Attachment');
     				
     			    $predicates[] = new AttachmentObjectPredicate($this->object_it);
     				
     			    $key_field = 'FileExt';
     				
     			    break;
     		}
    
     		if ( is_object($file) )
     		{
     			$additional = "";
     			
     			if ( preg_match( '/style="([^"]+)"/i', $attributes, $attrs ) )
     				$additional .= 'style="'.$attrs[1].'" ';
    
     			if ( preg_match( '/width="([^"]+)"/i', $attributes, $attrs ) )
     				$additional .= 'width="'.$attrs[1].'" ';
    
     			if ( preg_match( '/class="([^"]+)"/i', $attributes, $attrs ) )
     				$additional .= 'class="'.$attrs[1].'" ';

     			$filter = new FilterAttributePredicate($key_field, $name);
     			$filter->setHasMultipleValues(false);
     			
     			$predicates[] = $filter;
     			
     			$file_it = $file->getRegistryBase()->Query( $predicates );

     			if ( $file_it->count() > 0 )
     			{
     				return '<img src="'.$file_it->getFileUrl().'" '.$additional.'>';
     			}
     		}
     	}
     	
     	return $match[0];
    }
}