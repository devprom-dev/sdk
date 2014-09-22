<?php

define('WIKI_MODE_NORMAL', 2);
define('WIKI_MODE_MINIMAL', 4);
define('WIKI_MODE_INLINE', 8);
define('WIKI_MODE_INPLACE_INPUT', 16);
 
abstract class WikiEditorBase
{
 	var $object, $object_it;
 	var $field = 'Content';
 	var $attachments_field;
 	var $mode = WIKI_MODE_NORMAL;
 	var $tabindex = 0;
	var $index;
	var $minimum_rows = 6;
	var $maximum_rows = 30;
	var $css_class_name = '';
	var $required = false;
 	
 	function WikiEditorBase( $object = null )
 	{
 		if ( is_object($object) )
 		{
	 		if ( is_subclass_of($object, 'IteratorBase') )
	 		{
	 			$this->object_it = $object;
	 			$this->object = $this->object_it->object; 
	 		}
	 		else
	 		{
	 			$this->object = $object;
	 		}
 		}
 	}
 	
 	function setObjectIt( $object_it )
 	{
 		$this->object_it = $object_it;
		$this->object = $this->object_it->object; 
 	}
 	
 	function getObjectIt()
 	{
 		return $this->object_it;
 	}

	function setObject( $object )
	{
		$this->object = $object;
	}
	
 	function getObject()
 	{
 		return $this->object;
 	}
 	
 	function setMode( $mode )
 	{
 		$this->mode = $mode;
 	}
 	
 	function getMode()
 	{
 		return $this->mode;
 	}
 	
 	function setTabIndex( $tabindex )
 	{
 		$this->tabindex = $tabindex;
 	}
 	
 	function getTabIndex()
 	{
 		return $this->tabindex;
 	}
 	
 	function setMinRows( $rows )
 	{
 		$this->minimum_rows = $rows;
 	}
 	
 	function getMinRows()
 	{
 		return $this->minimum_rows;
 	}
 	
 	function setMaxRows( $rows )
 	{
 		$this->maximum_rows = $rows;
 	}
 	
 	function getMaxRows()
 	{
 		return $this->maximum_rows;
 	}
 	
 	function setCssClassName( $class )
 	{
 	    $this->css_class_name = $class;
 	}
 	
 	function getCssClassName()
 	{
 	    return $this->css_class_name;
 	}
 	
 	function hasInlineEditingCapabilities()
 	{
 	    return false;
 	}
 	
 	function hasHelpSection()
 	{
 		return false;
 	}
 	
 	function getDisplayName()
 	{
 		return '';
 	}
 	
 	function setFieldName( $field )
 	{
 		$this->field = $field;
 	}
 	
 	function getFieldName()
 	{
 		return $this->field;
 	}
 	
 	function setFieldId( $field )
 	{
 		$this->index = $field;
 	}
 	
 	function getFieldId()
 	{
		return $this->index != '' ? $this->index : $this->getFieldName();
 	}
 	
 	function setRequired( $required )
 	{
 	    $this->required = $required;
 	}

  	function getRequired()
 	{
 	    return $this->required;
 	}
 	
 	function setAttachmentsField( $field )
 	{
 		$this->attachments_field = $field;
 	}
 	
 	function getAttachmentsField()
 	{
 		return $this->attachments_field;
 	}
 	
  	function getAttachmentsCallback()
 	{
 		return "makeClickableFiles";
 	}
 	
 	function getTemplateCallback()
 	{
 		return "function(result) { $('#".$this->getFieldId()."').val(result); }"; 
 	}
 	
 	function getPageParser()
 	{
 		return null;
 	}
 	
 	function getHtmlParser()
 	{
 		return null;
 	}

 	function getComparerParser()
 	{
 		return null;
 	}
 	
 	function getDiff( $prev_content, $current_content )
 	{
 		include_once SERVER_ROOT_PATH."ext/diff/finediff.php";
 		
 		$diff = new FineDiff (
 				IteratorBase::wintoutf8($prev_content), 
 				IteratorBase::wintoutf8($current_content), 
 				array(
					FineDiff::paragraphDelimiters,
					FineDiff::sentenceDelimiters,
					FineDiff::wordDelimiters
				)
		);
 		
 		$has_changes = false;
 		
 		foreach( $diff->getOps() as $operation )
 		{
 			if ( $operation instanceof FineDiffCopyOp ) continue;
 			
 			$has_changes = true;
 			
 			break;
 		}
 		
 		return $has_changes
 			? nl2br(IteratorBase::utf8towin(html_entity_decode($diff->renderDiffToHtml(), ENT_COMPAT | ENT_HTML401, 'UTF-8'))) 
 			: "";
 	}
 	
 	function getDiff2( $prev_content, $current_content )
 	{
		require_once(SERVER_ROOT_PATH.'ext/diff/prepend.php');
		require_once(SERVER_ROOT_PATH.'ext/diff/diff.php');

		if($current_content != '')
			$curr_content_array = preg_split('#\R+#', IteratorBase::wintoutf8($current_content), -1, PREG_SPLIT_NO_EMPTY);
		else
			$curr_content_array = array('');

		if($prev_content != '')
			$prev_content_array = preg_split('#\R+#', IteratorBase::wintoutf8($prev_content), -1, PREG_SPLIT_NO_EMPTY);
		else
			$prev_content_array = array('');
			
		$diff = new Diff($prev_content_array, $curr_content_array);
		
		$html = HTML::div(array('id'=>'content'));
 
        if ($diff->isEmpty()) 
        {
            return '';
        }
        else
        {
            $fmt = new HtmlUnifiedDiffFormatter();

            $html->pushContent($fmt->format($diff));

        	return IteratorBase::utf8towin($html->asXml());
        }
 	}
 	 	
	function drawPreviewButton()
	{
	}
	
	function getDescription()
	{
		global $model_factory;
		
		$info = $model_factory->getObject('Module')->getExact('project-settings')->buildMenuItem();
		
		return str_replace('%1', $info['url'], text(606));
	}
	
 	function drawHelpSection()
 	{
	?>
		<tr>
			<td class="wiki_sub">
				<? echo text(1088) ?>
			</td>
		</tr>
		<tr>
			<td valign=top>
				<? echo text(606) ?>
			</td>
		</tr>
	<?php  		
 	}
 	
 	function drawScripts()
 	{
 	}
 	
 	public function getExportActions( $object_it )
 	{
 		return array();
 	}
 	
 	abstract function draw( $content, $b_editable = false );
}
 