<?php

include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';

class FormBlogAttachmentEmbedded extends PMFormEmbedded
{
    var $image_mode = false;
    var $anchor_it;

    function setAnchorIt( $object_it )
    {
        $this->anchor_it = $object_it;
    }

    function getFieldValue( $attr )
    {
        switch( $attr )
        {
            case 'BlogPost':
                return $this->anchor_it->getId();
                 
            default:
                return parent::getFieldValue( $attr );
        }
    }

    function IsAttributeVisible( $attribute )
    {
        switch ( $attribute )
        {
            case 'Content':
                return true;
                	
            default:
                return false;
        }
    }

    function drawFieldTitle( $attr )
    {
    }

    function setObjectIt( $object_it )
    {
        $iterator = $this->getIteratorRef();
        
        $iterator->setStop( 'BlogPost', $object_it->getId() );
        	
        parent::setObjectIt( $object_it );
    }

    function setAddButtonText( $text )
    {
        parent::setAddButtonText( $text );
        	
        $this->image_mode = true;
    }

    function getItemDisplayName( $object_it )
    {
        if ( !$this->image_mode )
        {
            return parent::getItemDisplayName( $object_it );
        }

        return $object_it->getFileLink();
    }

    function getShowMenu()
    {
        return !$this->image_mode;
    }

    function getSaveCallback()
    {
        return 'callbackFileAttached';
    }

    function drawScripts()
    {
        $editor = WikiEditorBuilder::build();
        
        ?>
     	<script type="text/javascript">
     		function callbackFileAttached( formid, data )
     		{
     			<?=$editor->getAttachmentsCallback()?>();
    
    			$('a.modify_image[name="'+data.name+'"]').each( function() {
     				if ( !$(this).parent().is('strike') ) $(this).click();
    			});
     		}
     	</script>
	<? 		
 	}
 }
