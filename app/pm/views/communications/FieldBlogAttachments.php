<?php

include_once SERVER_ROOT_PATH.'pm/views/ui/FieldAttachments.php';
include 'FormBlogAttachmentEmbedded.php';

class FieldBlogAttachments extends FieldAttachments
{
    var $form;

    function getForm()
    {
        global $model_factory;

        if ( is_object($this->form) )
        {
            return $this->form;
        }

        $files = $model_factory->getObject('BlogPostFile');

        $object_it = $this->getObjectIt();

        if ( $object_it->getId() > 0 )
        {
            $files->addFilter( new BlogPostFilePostFilter($object_it->getId()) );
        }
        else
        {
            $files->addFilter( new BlogPostFilePostFilter(0) );
        }
        	
        $this->form = new FormBlogAttachmentEmbedded( $files, 'BlogPost' );

        $this->form->setAnchorIt( $object_it );

 		$this->form->setReadonly( $this->readOnly() );
        
        if ( !$this->getEditMode() ) $this->form->setObjectIt( $object_it );

        return $this->form;
    }
}