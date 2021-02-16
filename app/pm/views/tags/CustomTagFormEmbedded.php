<?php
include_once "TagFormEmbedded.php";

class CustomTagFormEmbedded extends TagFormEmbedded
{
     var $anchor_it;

     function setAnchorIt( $object_it )
     {
         $this->anchor_it = $object_it;
     }
     
   	 function getFieldValue( $attr )
     {
         $object = $this->getObject();
         
         switch ( $attr )
         {
             case 'ObjectId':
                 return is_object($this->anchor_it) ? $this->anchor_it->getId() : '';

             case 'ObjectClass':
                 $anchor_object = $object->getObject();
                 
                 return strtolower(get_class($anchor_object));
                 
             default:
                 return parent::getFieldValue( $attr );
         }
     }
}
