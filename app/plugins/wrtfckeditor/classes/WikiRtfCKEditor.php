<?php
 
include_once SERVER_ROOT_PATH."pm/views/wiki/editors/WikiEditorBase.php";

include 'WrtfCKEditorWikiParser.php';
include 'WrtfCKEditorPageParser.php';
include 'WrtfCKEditorHtmlParser.php';
include "WrtfCKEditorComparerParser.php";
 
class WikiRtfCKEditor extends WikiEditorBase
{
 	function hasInlineEditingCapabilities()
 	{
 	    return true;
 	}
    
 	function getDisplayName()
 	{
 		return text('wrtfckeditor1');
 	}
 	
 	function hasHelpSection()
 	{
 		return false;
 	}
 	
	function drawPreviewButton()
	{
	}

	function getBaseParser( $original_editor = '' )
	{
	    $object_it = $this->getObjectIt();
	     
	    if ( $original_editor != '' )
	    {
	        $editor = $original_editor;
	    }
	    else if ( is_object($object_it) && $object_it->count() > 0 )
	    {
	     	$editor = strtolower($object_it->get('ContentEditor'));
	    }
	    
	    if ( strtolower($editor) == 'wikisyntaxeditor' )
	    {
	        return new WrtfCKEditorWikiParser( $object_it );
	    }
	}
	
 	function getPageParser( $original_editor = '' )
 	{
 	    $base_editor = $this->getBaseParser($original_editor);
 	    
 	    if ( is_object($base_editor) ) return $base_editor;
 	    
    	return new WrtfCKEditorPageParser( $this->getObjectIt() );
 	}

 	function getEditorParser( $original_editor = '' )
 	{
 	    $base_editor = $this->getBaseParser($original_editor);
 	    
 	    if ( is_object($base_editor) ) return $base_editor;
 	    
    	return new WrtfCKEditorPageParser( $this->getObjectIt() );
 	}

 	function getHtmlParser()
 	{
 	    $base_editor = $this->getBaseParser($original_editor);
 	    
 	    if ( is_object($base_editor) ) return $base_editor;
 	    
    	return new WrtfCKEditorHtmlParser( $this->getObjectIt() );
 	}

 	function getComparerParser()
 	{
 		return new WrtfCKEditorComparerParser( $this->getObjectIt() );
 	}
 	
 	function getTemplateCallback()
 	{
 		return "function(result) { pasteTemplate( '".$this->getFieldId()."', result ); }"; 
 	}
  	
  	function getAttachmentsCallback()
 	{
 		return "addImagesAutomatically";
 	}
 	
 	public function getExportActions( $object_it )
 	{
 		$actions = array();
 		
 		if ( $object_it->object instanceof ProjectPage ) return $actions;
 		
		$method = new WysiwygExportWordWebMethod();
		
		$actions[] = array( 
			'name' => $method->getCaption(), 
			'url' => $method->getJSCall(array()) 
		);
 		
		return $actions;
 	}
 	
 	function draw( $content, $b_editable = false )
 	{
 		$lang = strtolower(getSession()->getLanguageUid());
 		
		$field = $this->getFieldName();

		$id = $this->getFieldId();
		
		$toolbar = $this->getMode() & WIKI_MODE_MINIMAL || !$b_editable 
			? "'MiniToolbar'" : "'FullToolbar'";
			
 		$rows = ($this->getMode() & WIKI_MODE_MINIMAL ? $this->getMinRows() : $this->getMaxRows());
 		
 		$height = $rows * 15;
 		 
 		$object_it = $this->getObjectIt();
 		
 		$object_id = is_object($object_it) ? $object_it->getId() : '';
 			
 		$modify_url = getSession()->getApplicationUrl().'methods.php?method=modifyattributewebmethod';
 		
 		$attachment_field = $this->getAttachmentsField();
 		
 		if ( is_object($attachment_field) )
 		{
 		    $attachment_field->setReadonly( false );
 		    
 			$form = $attachment_field->getForm();
 			
 			$form->drawScripts();
 			
 			$form->setAddButtonText( translate('загрузить изображение') );
 			
 			ob_start();
 			
 			echo wordwrap(text(1281), 60, '<br/>');
 			echo '<br/><br/>';
 			

 			$attachment_field->draw();

 			echo '<br/><br/>';
 			echo wordwrap(text(1282), 60, '<br/>');
 			
 			$attachments_html = ob_get_contents();
 			
 			ob_end_clean();
 		}
 		else
 		{
 			$attachments_html = '';
 		}
 		
		?>
		
		<div>
			<?php if ( $b_editable ) { ?>
			
			<?php if ( !($this->getMode() & WIKI_MODE_INLINE) ) { ?>
			
			<textarea class="input-block-level wysiwyg <?=$this->getCssClassName()?>" tabindex="<?php echo $this->getTabIndex(); ?>" id="<?php echo $id; ?>" rows="<?=$rows?>" objectId="<?=$object_id?>" name="<?php echo $field; ?>" <?=($this->getRequired() ? 'required' : '')?> ><? echo $content; ?></textarea>
			
			<?php } else { ?>
			
			<input type="hidden" id="<?php echo $id; ?>Value" name="<?php echo $field; ?>" value="<?=htmlentities($content, ENT_QUOTES | ENT_HTML401, APP_ENCODING)?>">

			<div class="reset wysiwyg <?=$this->getCssClassName()?>" style="min-height:<?=$height?>px;" contenteditable="true" objectId="<?=$object_id?>" tabindex="<?php echo $this->getTabIndex(); ?>" id="<?php echo $id; ?>" <?=($this->getRequired() ? 'required' : '')?> >
			    <? 
				        // decode is required because of edit mode is displayed like html (div)
			        echo html_entity_decode($content, ENT_QUOTES | ENT_HTML401, APP_ENCODING); 
			    ?>
			</div>
			
			<?php } ?>
			
			<?php } elseif ( $this->getMode() & WIKI_MODE_INPLACE_INPUT ) { ?>
			
			<div class="wysiwyg-text wysiwyg-input <?=$this->getCssClassName()?>" objectClass="<?=get_class($object_it->object)?>" objectId="<?=$object_id?>" attributeName="<?=$field?>" contenteditable="true" id="<?php echo $id; ?>" <?=($this->getRequired() ? 'required' : '')?> ><? echo $content; ?></div>
			
			<?php } else { ?>

			<?php if ( $content == '' ) { ?>
			
		    <div class="wysiwyg-welcome" for-id="<?=$id?>"><?=text(1280)?></div>
			
			<?php } ?>
			
			<?php if ( $content == '' ) $style = 'min-height:'.$height.'px;'; ?>
			
			<div class="reset wysiwyg <?=$this->getCssClassName()?>" style="<?=$style?>" objectClass="<?=get_class($object_it->object)?>" objectId="<?=$object_id?>" attributeName="<?=$field?>" contenteditable="true" id="<?php echo $id; ?>" <?=($this->getRequired() ? 'required' : '')?> > <? echo $content; ?></div>
			
			<?php } ?>

		</div>

		<script type="text/javascript">
			$(document).ready( function() 
			{
				setup<?=$id?>();
			
				if ( $('#<?=$id?>').is('.wysiwyg-text') )
				{

					$('#<?=$id?>.wysiwyg-text[contenteditable="true"]').parent()
						.hover(function() { 
							if ( !$(this).find('.wysiwyg-text').is('.cke_focus') ) $(this).addClass('wysiwyg-hover');
							$('.wysiwyg-welcome[for-id='+$(this).attr('id')+']').css('border-top', '2px solid white'); 
						}, 
						function() { 
							$(this).removeClass('wysiwyg-hover'); 
							$('.wysiwyg-welcome[for-id='+$(this).attr('id')+']').css('border', 'none'); 
						});
 				}
		    });

		    function setup<?=$id?>()
		    {
		    	if ( $('#<?=$id?>').is('.cke_editable') ) return;
		    	
				setupWysiwygEditor(
	    			'<?=$id ?>', 
	    			<?=($this->getMode() & WIKI_MODE_INPLACE_INPUT ? "''" : $toolbar) ?>, 
				    '<?=$height ?>', 
				    '<?=$modify_url ?>', 
				    <?=JsonWrapper::encode(htmlentities(IteratorBase::wintoutf8($attachments_html), ENT_QUOTES | ENT_HTML401, 'UTF-8')) ?>, 
				    '<?=$_SERVER['APP_VERSION'] ?>'
				);
		    }
		</script>
		
		<?php
 	}
}