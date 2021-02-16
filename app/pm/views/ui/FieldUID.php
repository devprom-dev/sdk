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
        echo '<span class="input-block-level well well-text uid-state">';
            echo $view->render('core/Clipboard.php', array ('url' => $info['url'], 'uid' => "{" . $info['project'] . "} " . $info['uid']));
            if ( $this->object_it->object instanceof MetaobjectStatable && getFactory()->getAccessPolicy()->can_modify_attribute($this->object_it->object, 'State') ) {
                echo $view->render('pm/StateColumn.php', array (
                    'stateIt' => $this->object_it->getStateIt(),
                    'actions' => $this->form->getTransitionActions()
                ));
            }
        echo '</span>';
	}
}