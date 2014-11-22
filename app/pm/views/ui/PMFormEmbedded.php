<?php

class PMFormEmbedded extends FormEmbedded
{
    var $customtypes;
    var $customkinds;

    function __construct( $object = null, $anchor_field = null, $form_field = '' )
    {
        parent::__construct( $object, $anchor_field, $form_field );
        	
        $this->customtypes = array();
        $this->customkinds = array();
        	
        if ( is_object($object) && getFactory()->getObject('CustomizableObjectSet')->checkObject($object) )
        {
            $it = getFactory()->getObject('pm_CustomAttribute')->getByEntity($object);
            
            while ( !$it->end() )
            {
                $this->customtypes[$it->get('ReferenceName')] = $it->getRef('AttributeType')->get('ReferenceName');
                	
                if ( $it->get('ObjectKind') != '' )
                    $this->customkinds[$it->get('ReferenceName')] = $it->get('ObjectKind');
                	
                $it->moveNext();
            }
        }
    }

    function IsAttributeObject( $attr )
    {
    	switch( $attr )
    	{
    	    default:
    	    	if ( $this->getObject()->getAttributeType($attr) == 'wysiwyg' )
    	    	{
    	    		return true;
    	    	}
    	    	
    	    	return parent::IsAttributeObject( $attr );
    	}
    }
    
    function createField( $attr )
    {
        switch ( $attr )
        {
            default:
                foreach ( $this->customtypes as $refname => $type )
                {
                    if ( $attr == $refname && $type == 'dictionary' )
                    {
                        return new FieldCustomDictionary( $this->getObject(), $refname );
                    }
                }
                
                if ( $this->getObject()->getAttributeType($attr) == 'wysiwyg')
                {
                    	$field = new FieldWYSIWYG();
    
                        $object_it = $this->getObjectIt();
    
                        is_object($object_it) ? $field->setObjectIt($object_it)
                                : $field->setObject($this->getObject());
    
                        $editor = $field->getEditor();
                        
    					$editor->setMode( WIKI_MODE_MINIMAL | WIKI_MODE_INLINE );
                        
                        return $field;
                }
                
                return parent::createField( $attr );
        }
    }
}