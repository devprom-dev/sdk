<?php

class FieldWikiPage extends FieldAutoCompleteObject
{
    function draw()
    {
    	global $tabindex;

		$this->setAutoExpand(false);
		
		$url = getSession()->getApplicationUrl().'wiki/select/';
		
    	$script = "bindFindInTreeField('.find-in-tree > .btn[field-id=".$this->getId()."]', '".$url."'); return false;";
    	
    	echo '<div style="display:table;width:100%;margin-bottom:8px;">';
	    	echo '<div style="display:table-cell;">';
	    		parent::draw();
	    	echo '</div>';
	    	echo '<div style="display:table-cell;">&nbsp;</div>';
	    	echo '<div class="find-in-tree" style="display:table-cell;width:150px;">';
	    		$tabindex++;
	    		
	        	echo '<button type="button" tabindex="'.$tabindex.'" field-id="'.$this->getId().'" class="btn btn-small btn-success" style="margin-top:4px;" onclick="javascript: '.$script.'">';
	            	echo '<i class="icon-search icon-white"></i> '.translate('Выбрать в дереве');
				echo '</button>';
			echo '</div>';
		echo '</div>';
		echo '<span class="input-block-level well well-text find-in-tree-area" style="display:none;" field-class="'.get_class($this->getObject()).'" field-id="'.$this->getId().'" field-name="'.$this->getName().'">';
			echo '<ul class="filetree" style="width:100%;">'.text(1708).'</ul>';
			echo '<div style="clear:both;"></div>';
		echo '</span>';
    }
}