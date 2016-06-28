<?php

class FieldHierarchySelector extends FieldAutoCompleteObject
{
	private $treeObject = null;

	function __construct( $object, $attributes = null ) {
		parent::__construct($object, $attributes);
		$this->setTreeObject($object);
	}

	function setTreeObject( $object ) {
		$this->treeObject = $object;
	}

    function draw( $view = null )
    {
    	global $tabindex;

		$this->setAutoExpand(false);
		
		if ( $this->Readonly() ) {
			parent::draw();
			return; 
		}
		
		$url = getSession()->getApplicationUrl().'treemodel/';
		$class = get_class($this->treeObject);
		if ( $this->getCrossProject()) {
			$class .= '?cross';
		}
		
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
		echo '<span class="input-block-level well well-text find-in-tree-area" style="display:none;" field-class="'.$class.'" field-id="'.$this->getId().'" field-name="'.$this->getName().'">';
			echo '<ul class="filetree" style="width:100%;">'.text(1708).'</ul>';
			echo '<div style="clear:both;"></div>';
		echo '</span>';
    }
}