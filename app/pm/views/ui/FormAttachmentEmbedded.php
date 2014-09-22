<?php

include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';

 /////////////////////////////////////////////////////////////////////////////////////////////////////
 class FormAttachmentEmbedded extends PMFormEmbedded
 {
 	var $image_mode = false;
 	var $anchor_it;
 	
 	function setAnchorIt( $object_it )
 	{
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
		
		return '<a class=modify_image id="File'.$object_it->getId().'" href="'.$object_it->getFileUrl().'" name="'.$object_it->getFileName('File').'">'.
			'<img src="/images/attach.png" style="margin-bottom:-4px;">&nbsp;'.$object_it->getFileName('File').
				' ('.$object_it->getFileSizeKb('File').' Kb)</a>'; 		
	}
	
	function getActions( $object_it, $item )
	{
	    $actions = parent::getActions( $object_it, $item );

	    $open = array( array (
	        'name' => translate('Открыть'),
	        'url' => "javascript: $('a[id=File".$object_it->getId()."]').click();"
	    ), array());
	    
	    return array_merge($open, $actions);
	}
	
 	
 	function getShowMenu()
 	{
		return !$this->image_mode;
 	}
 	
 	function getSaveCallback()
	{
	    if ( !$this->image_mode ) return '';
	    
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
 
 /////////////////////////////////////////////////////////////////////////////////////////////////////

 
   