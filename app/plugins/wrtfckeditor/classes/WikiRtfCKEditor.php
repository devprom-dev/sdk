<?php
 
include_once SERVER_ROOT_PATH."pm/views/wiki/editors/WikiEditorBase.php";
include 'WrtfCKEditorWikiParser.php';
include 'WrtfCKEditorPageParser.php';
include 'WrtfCKEditorHtmlParser.php';
include 'WrtfCKEditorHtmlImportableParser.php';
include "WrtfCKEditorComparerParser.php";
include "WrtfCKEditorSupportParser.php";

class WikiRtfCKEditor extends WikiEditorBase
{
	function __construct()
	{
		parent::__construct();
		$this->setToolbar(self::ToolbarMini);
	}

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
        return new WrtfCKEditorHtmlParser( $this->getObjectIt() );
	}
	
 	function getPageParser( $original_editor = '' )
 	{
    	return new WrtfCKEditorPageParser( $this->getObjectIt() );
 	}

 	function getEditorParser( $original_editor = '' )
 	{
    	return new WrtfCKEditorPageParser( $this->getObjectIt() );
 	}

 	function getHtmlParser()
 	{
    	return new WrtfCKEditorHtmlParser( $this->getObjectIt() );
 	}

    function getHtmlImportableParser()
    {
        return new WrtfCKEditorHtmlImportableParser( $this->getObjectIt() );
    }

 	function getComparerParser()
 	{
 		return new WrtfCKEditorComparerParser( $this->getObjectIt() );
 	}
 	
  	function getAttachmentsCallback()
 	{
 		return "addImagesAutomatically";
 	}
 	
 	function draw( $content, $b_editable = false )
 	{
		if ( is_object($this->getObjectIt()) && $this->getObjectIt()->getId() != '' ) {
			$projectCodeName = $this->getObjectIt()->get('ProjectCodeName');
		}
		if ( $projectCodeName == '' ) {
            $projectCodeName = getSession()->getProjectIt()->get('CodeName');
        }

		$field = $this->getFieldName();

		$id = $this->getFieldId();
		
 		$rows = $this->getMinRows();
 		$height = $rows * 16.9;
		$toolbar = $this->getToolbar() == self::ToolbarMini ? "MiniToolbar" : "FullToolbar";
 		$object_it = $this->getObjectIt();
 		$object_id = is_object($object_it) ? $object_it->getId() : '';
 		$version = is_object($object_it) ? $object_it->get('RecordVersion') : '';

 		$attributes = array(
            'modifyUrl' => getSession()->getApplicationUrl().'methods.php?method=modifyattributewebmethod',
            'toolbar' => ($this->getMode() & WIKI_MODE_INPLACE_INPUT ? "" : $toolbar),
            'objectId' => $object_id,
            'id' => $id,
            'objectClass' => get_class($this->getObject()),
            'project' => $projectCodeName,
            'annotation' => $object_it instanceof WikiPageIterator ? $object_it->getAnnotationData() : '',
            'attributeName' => $field,
            'tabindex' => $this->getTabIndex(),
            'version' => $version,
            'userHeight' => $height,
            'appVersion' => $_SERVER['APP_VERSION']
        );

        $attributesAttr = implode(' ',
            array_map( function ($k, $v) {
                return $k .'="'. htmlspecialchars($v) .'"';
                }, array_keys($attributes), $attributes
        ));

		?>
		
		<div class="editor-area">
			<?php if ( $b_editable ) { ?>
			
			<?php if ( !($this->getMode() & WIKI_MODE_INLINE) ) { ?>
			
			<textarea class="input-block-level wysiwyg <?=$this->getCssClassName()?>" <?=$attributesAttr?> rows="<?=($rows)?>" name="<?=$field;?>" <?=($this->getRequired() ? 'required' : '')?> ><? echo $content; ?></textarea>

			<?php } else { ?>
			
			<input type="hidden" id="<?php echo $id; ?>Value" name="<?php echo $field; ?>" value="<?=$content?>">

            <textarea class="input-block-level <?=$this->getCssClassName()?>" tabindex="<?php echo $this->getTabIndex(); ?>" name="<?=$field;?>" rows="<?=($rows)?>" <?=$attributesAttr?> <?=($this->getRequired() ? 'required' : '')?> ><? echo $content; ?></textarea>

			<?php } ?>
			
			<?php } elseif ( $this->getMode() & WIKI_MODE_INPLACE_INPUT ) { ?>
			
			<div class="wysiwyg-text wysiwyg-input <?=$this->getCssClassName()?>" <?=$attributesAttr?> contenteditable="true" <?=($this->getRequired() ? 'required' : '')?> >
                <? echo $content; ?>
            </div>
			
			<?php } else { ?>

			<?php if ( $content == '' ) { ?>
			
		    <div class="wysiwyg-welcome hidden-print" for-id="<?=$id?>"><?=text(1280)?></div>

            <?php } ?>

			<div class="reset wysiwyg <?=$this->getCssClassName()?>" contenteditable="true" <?=$attributesAttr?> <?=($this->getRequired() ? 'required' : '')?> >
                <? echo html_entity_decode($content, ENT_QUOTES | ENT_HTML401, APP_ENCODING); ?>
            </div>
			
			<?php } ?>
		</div>
		<?php
 	}
}