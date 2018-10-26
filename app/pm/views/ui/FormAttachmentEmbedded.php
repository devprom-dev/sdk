<?php
include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';

class FormAttachmentEmbedded extends PMFormEmbedded
{
 	var $image_mode = false;
 	var $anchor_it;
 	private $image_class = '';
 	
 	function setAnchorIt( $object_it ) {
 	    $this->anchor_it = $object_it;
 	}
 	
 	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'File':
 			case 'Description':
 				return true;
 				
 			default:
 				return false;
 		}
 	}
 	
 	function drawFieldTitle( $attr )
 	{
 	}
 	
	function getFieldValue( $attr )
	{
	    switch( $attr )
	    {
	        case 'ObjectId':
	            return $this->anchor_it->getId();
	            
	        case 'ObjectClass':
	            return strtolower(get_class($this->anchor_it->object));
	            
	        default:
	            return parent::getFieldValue( $attr );
	    }
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

		return '<a class="image_attach" data-fancybox="gallery" href="'.$object_it->getFileUrl().'" style="padding-bottom:6px;" name="'.$object_it->getFileName('File').'">'.
			'<img src="/images/attach.png" style="margin-bottom:-4px;"> '.$object_it->getFileName('File').'</a>'.
				' ('.$object_it->getFileSizeKb('File').' Kb)'; 		
	}

 	function getSaveCallback()
	{
	    if ( !$this->image_mode ) return '';
	    
		return 'callbackFileAttached';
	}

     function drawAddButton( $view, $tabindex )
     {
         if ( !$this->getReadonly() && getFactory()->getAccessPolicy()->can_create($this->getObject()) ) {
             echo '<a class="dashed embedded-add-button" tabindex="' . $tabindex . '" href="javascript: appendEmbeddedItem(' .
                 $this->getFormId() . ');">' . $this->getAddButtonText() . '</a>';
         }
     }

  	function drawScripts()
 	{
 	    $editor = WikiEditorBuilder::build();
 	    
     	?>
     	<script type="text/javascript">
     		function callbackFileAttached( formid, data )
     		{
     			<?=$editor->getAttachmentsCallback()?>();
    
    			$('.image-link[name="'+data.name+'"]').each( function() {
     				if ( !$(this).parent().is('strike') ) $(this).click();
    			});
     		}
     	</script>
    	<? 		
 	}

 	function getTitleTemplate()
    {
        return 'core/EmbeddedRowAttachmentMenu.php';
    }
}
