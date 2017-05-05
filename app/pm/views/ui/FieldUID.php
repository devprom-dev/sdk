<?php

class FieldUID extends FieldShortText
{
	private $object_it = null;
	private $form = null;

	function __construct( $formObject )
	{
		parent::__construct();
		$this->form = $formObject;
		$this->object_it = $formObject->getObjectIt();
	}

	function render( $view )
	{
		$uid = new ObjectUID();
		$info = $uid->getUIDInfo($this->object_it);
		echo '<div class="input-block-level well well-text">';
		    echo '<span class="pull-left">';
			    echo $view->render('core/Clipboard.php', array ('url' => $info['url'], 'uid' => $info['uid']));
            echo '</span>';
            echo '<span class="pull-right uid-state">';
                echo $view->render('pm/StateColumn.php', array (
                    'color' => $this->object_it->get('StateColor'),
                    'name' => $this->object_it->get('StateName'),
                    'terminal' => $this->object_it->get('StateTerminal') == 'Y',
                    'actions' => $this->form->getTransitionActions()
                ));
            echo '</span>';
        echo '</div>';
	}
}