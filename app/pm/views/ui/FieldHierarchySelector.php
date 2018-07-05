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

		$count = $this->treeObject->getRegistry()->Count(
		    array(
		        new FilterVpdPredicate()
            )
        );
    	$script = "bindFindInTreeField('.btn[field-id=".$this->getId()."]', '".$url."', '".($count < 40 ? 'expand': '')."'); return false;";
    	
    	echo '<div style="display:table;width:100%;">';
	    	echo '<div style="display:table-cell;">';
	    		parent::draw();
	    	echo '</div>';
	    	echo '<div style="display:table-cell;">&nbsp;</div>';
	    	echo '<div class="find-in-tree" style="display:table-cell;width:150px;">';
	    		$tabindex++;
	    		
	        	echo '<button type="button" tabindex="'.$tabindex.'" field-id="'.$this->getId().'" class="btn btn-small btn-success" onclick="javascript: '.$script.'">';
	            	echo '<i class="icon-search icon-white"></i> '.translate('Выбрать в дереве');
				echo '</button>';
			echo '</div>';
		echo '</div>';
		echo '<span class="input-block-level well well-text find-in-tree-area" style="display:none;margin-top: 10px;" field-class="'.$class.'" field-id="'.$this->getId().'" field-name="'.$this->getName().'">';
			echo '<ul class="filetree" style="width:100%;">'.text(1708).'</ul>';
	    	echo '<div style="clear:both;"></div>';

            if ( $this->getCrossProject()) {
                $script = "bindFindInTreeField('.btn[field-id=more".$this->getId()."]', '".$url."', ''); return false;";
                echo '<button type="button" tabindex="' . ($tabindex + 1) . '" field-id="more' . $this->getId() . '" class="btn btn-link btn-transparent" onclick="javascript: ' . $script . '" style="padding-left: 0;padding-top: 12px;">';
                    echo text(2505);
                echo '</button>';

                echo '<span class="find-in-tree-area" style="display:none;margin-top: 10px;" field-class="' . $class . '?cross" field-id="more' . $this->getId() . '" field-name="' . $this->getName() . '">';
                    echo '<ul class="filetree" style="width:100%;">' . text(1708) . '</ul>';
                    echo '<div style="clear:both;"></div>';
                echo '</span>';
            }
		echo '</span>';
    }
}