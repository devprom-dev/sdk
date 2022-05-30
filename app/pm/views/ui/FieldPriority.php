<?php

class FieldPriority extends Field
{
	private $actions;
	private $moreActions;

	function __construct( $object_it = null, $moreActions = array() ) {
        $this->object_it = $object_it;
        $this->moreActions = $moreActions;
		$this->actions = $this->buildActions();
	}

	function getActions() {
		return $this->actions;
	}

	function getObjectIt() {
		return $this->object_it;
	}

	function setObjectIt( $object_it ) {
		$this->object_it = $object_it;
	}

	function draw( $view = null )
	{
		foreach( $this->actions as $key => $action )
		{
			$method = $action['method'];
			$method->setObjectIt($this->object_it);
			$this->actions[$key]['url'] = $method->getJSCall();
		}
        unset($this->moreActions['modify']);

		$priorityIt = $this->object_it->getRef('Priority');

		$color = $priorityIt->get('RelatedColor') == '' ? 'white' : $priorityIt->get('RelatedColor');
		$textColor = '<span class="btn btn-priority btn-field"><span class="pri-cir" style="color:'.$color.'">&#x25cf;</span>&nbsp;</span>';

		echo $view->render('pm/AttributeButton.php', array (
			'data' => $textColor . $priorityIt->getDisplayName(),
			'items' => count($this->moreActions) > 0
                ? array_merge(
                        $this->actions,
                        array(array()),
                        $this->moreActions
                    )
                : $this->actions,
			'random' => $this->object_it->getId()
		));
	}

	protected function buildActions()
	{
		$actions = array();
        $empty_it = $this->object_it;

        $priorityIt = getFactory()->getObject('Priority')->getAll();
		while( !$priorityIt->end() )
		{
			$method = new ModifyAttributeWebMethod($empty_it, 'Priority', $priorityIt->getId());
			if ( $method->hasAccess() ) {
				$actions[$priorityIt->getId()] = array(
                    'name' => $priorityIt->getDisplayName(),
                    'method' => $method
				);
			}
            $priorityIt->moveNext();
		}
		return $actions;
	}
}
