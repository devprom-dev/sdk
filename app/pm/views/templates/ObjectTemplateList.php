<?php

class ObjectTemplateList extends PMPageList
{
     function IsNeedToDisplay( $attr )
     {
         switch( $attr )
         {
             case 'RecordCreated':
                 return true;

             case 'ListName':
                 return false;
                 
             default:
                 return parent::IsNeedToDisplay( $attr );
         }
     }
     
	function drawCell( $object_it, $attr ) 
	{
		parent::drawCell( $object_it, $attr );			
	}
}
 