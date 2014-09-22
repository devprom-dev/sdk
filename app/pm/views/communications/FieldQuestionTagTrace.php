<?php

include_once SERVER_ROOT_PATH."pm/views/tags/FieldTagTrace.php";
include_once SERVER_ROOT_PATH."pm/views/tags/CustomTagFormEmbedded.php";

class FieldQuestionTagTrace extends FieldTagTrace
{
     function __construct( $anchor )
     {
         parent::__construct( $anchor, 'ObjectId' );
     }
 
     function getTagObject()
     {
         global $model_factory;
 
         $tag = $model_factory->getObject('QuestionTag');
 
         $anchor_it = $this->getAnchorIt();
         	
         $tag->addFilter(
                 new FilterAttributePredicate( 'ObjectId',
                         is_object($anchor_it) && $anchor_it->getId() > 0 ? $anchor_it->getId() : 0 ) );
 
         return $tag;
     }
 
     function getForm()
     {
         $form = new CustomTagFormEmbedded( $this->getTagObject(), $this->getField() );
 
         $anchor_it = $this->getAnchorIt();
 
         $form->setAnchorIt( $anchor_it );
 
         return $form;
     }
}
