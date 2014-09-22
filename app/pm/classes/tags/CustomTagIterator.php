<?php

class CustomTagIterator extends TagIterator
{
     function get( $attr )
     {
         switch( $attr )
         {
             case 'Caption':
                 $caption = parent::get( $attr );
                 
                 if( $caption != '' ) return $caption;
                 	
                 $tag_name = parent::get( 'Tag' );
                 
                 $tag = $this->object->getAttributeObject('Tag');
                 
                 $tag_it = is_numeric($tag_name) && $tag_name > 0
                     ? $tag->getExact($tag_name)
                     : $tag->getEmptyIterator();
                 
                 return $tag_it->getId() > 0 ? $tag_it->get('Caption') : $tag_name;
                 
                 break;
                 	
             default:
                 return parent::get( $attr );
         }
     }
}
