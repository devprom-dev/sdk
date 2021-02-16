<?php

class FieldHierarchySelector extends FieldAutoCompleteObject
{
	private $treeObject = null;
	private $createParameters = array();
	private $multiselect = false;

	function __construct( $object, $attributes = null ) {
		parent::__construct($object, $attributes);
		$this->setTreeObject($object);
		if ( $object instanceof WikiPage ) {
            $this->setCrossProject();
        }
	}

	function setTreeObject( $object ) {
		$this->treeObject = $object;
	}

	function setCreateParameters( $parms ) {
	    $this->createParameters = $parms;
    }

    function setMultiselect($value = true) {
	    $this->multiselect = $value;
    }

    function draw( $view = null )
    {
    	global $tabindex;

		$this->setAutoExpand(false);
		
		if ( $this->Readonly() ) {
			parent::draw();
			return; 
		}
		
		$url = getSession()->getApplicationUrl().'treemodel/'.get_class($this->treeObject);
		$queryParms = array();
		if ( get_class($this->treeObject) != get_class($this->getObject()) ) {
            $queryParms['selectableClass'] = get_class($this->getObject());
        }
		if ( count($queryParms) > 0 ) {
		    $url .= '?' . http_build_query($queryParms);
        }

    	$script = "bindFindInTreeField('.btn[field-id=".$this->getId()."]', '".$url."', ".($this->multiselect ? '2' : '1')."); return false;";
    	$submitScript = "submitFindInTreeField('.btn[field-id=".$this->getId()."]'); return false;";
    	
    	echo '<div style="display:table;width:100%;">';
	    	echo '<div style="display:table-cell;">';
	    		parent::draw();
	    	echo '</div>';
	    	echo '<div class="find-in-tree" style="display:table-cell;width:1%;white-space:nowrap;padding-left: 6px;">';
	    		$tabindex++;
	        	echo '<button type="button" tabindex="'.$tabindex.'" field-id="'.$this->getId().'" class="btn btn-sm btn-success" onclick="javascript: '.$script.'">';
	            	echo '<i class="icon-search icon-white"></i> '.translate('Выбрать в дереве');
				echo '</button>';

				if ( count($this->createParameters) > 0 ) {
                    $method = new ObjectCreateNewWebMethod($this->treeObject);
                    if ( $method->hasAccess() ) {
                        $tabindex++;
                        echo ' <button type="button" tabindex="'.$tabindex.'" class="btn btn-sm btn-success" onclick="'.$method->getJSCall($this->createParameters).'" title="'.translate('Создать').'">';
                            echo '<i class="icon-plus icon-white"></i> ';
                        echo '</button>';
                    }
                }
			echo '</div>';
		echo '</div>';
		echo '<span class="input-block-level well well-text find-in-tree-area" style="display:none;margin-top: 10px;" field-id="'.$this->getId().'" field-name="'.$this->getName().'">';
			echo '<div class="filetree" style="width:100%;"></div>';
	    	if ( !$this->multiselect ) {
                echo '<div style="padding:6px;"><a class="btn btn-primary btn-xs" onclick="javascript: '.$submitScript.'">'.translate('Выбрать').'</a></div>';
            }
		echo '</span>';
    }
}