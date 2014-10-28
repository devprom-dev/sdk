<?php

include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';

class FormWikiAttachmentEmbedded extends PMFormEmbedded
{
 	var $image_mode = false;
 	var $anchor_it;
 	
 	private $image_class = "image_attach";
 	
 	function setAnchorIt( $object_it )
 	{
 	    $this->anchor_it = $object_it;
 	}
 	
 	function setImageClass( $class_name )
 	{
 		$this->image_class = $class_name;
 	}

 	function getFieldValue( $attr )
 	{
 	    switch( $attr )
 	    {
 	        case 'WikiPage':
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

		return '<a class="'.$this->image_class.'" href="'.$object_it->getFileUrl().'" name="'.$object_it->getFileName('Content').'">'.
			'<img src="/images/attach.png" style="margin-bottom:-4px;"> '.$object_it->getFileName('Content').'</a>'.
				' ('.$object_it->getFileSizeKb('Content').' Kb)'; 		
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
    
    			$('a.<?=$this->image_class?>[name="'+data.name+'"]').each( function() {
     				if ( !$(this).parent().is('strike') ) $(this).click();
    			});
     		}
     	</script>
    	<? 		
 	}
}
