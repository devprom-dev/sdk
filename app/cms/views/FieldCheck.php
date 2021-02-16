<?php

class FieldCheck extends Field
{
 	var $checkName;
 	private $showPopupMenu = false;
 	private $objectIt = null;
	
	function __construct( $checkText )
	{
		$this->checkName = $checkText;
		parent::__construct();
	}

	function setCheckName( $checkText ) {
		$this->checkName = $checkText;
	}

    function getCheckName() {
        return $this->checkName;
    }

	function readOnly() {
	    return !$this->getEditMode() || parent::readOnly();
	}

	function showPopupMenu( $value = true ) {
	    $this->showPopupMenu = $value;
    }

    function setObjectIt( $objectIt ) {
	    $this->objectIt = $objectIt;
    }
	
 	function draw( $view = null )
	{
	    $text = translate($this->checkName);
	    if ( $this->showPopupMenu ) {
            $text = $view->render('pm/AttributeButton.php', array (
                'data' => $text,
                'items' => $this->buildActions(),
                'extraClass' => 'btn btn-xs btn-light'
            ));
        }
	    ?>
		<label class="checkbox">
			<input type="hidden" name="<? echo $this->getName().'OnForm'; ?>" value="Y"> 
			<input id="<? echo $this->getId() ?>" tabindex="<? echo $this->getTabIndex() ?>" class="checkbox" name="<? echo $this->getName(); ?>" type="checkbox" <? if($this->getValue() == 'Y' || $this->getValue() == 'on') echo 'checked'; ?>
			   <? echo ($this->readOnly() ? 'class="readonly" disabled' : '') ?> >
                <?=$text?>
		</label>
	    <?
	}

    protected function buildActions()
    {
        $actions = array();
        $method = new ModifyAttributeWebMethod($this->objectIt, $this->getName(), 'Y');
        if ( $method->hasAccess() ) {
            $actions[] = array(
                'name' => translate('Да'),
                'url' => $method->getJSCall()
            );
        }
        $method = new ModifyAttributeWebMethod($this->objectIt, $this->getName(), 'N');
        if ( $method->hasAccess() ) {
            $actions[] = array(
                'name' => translate('Нет'),
                'url' => $method->getJSCall()
            );
        }
        return $actions;
    }
}
