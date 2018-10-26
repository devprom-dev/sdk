<?php

class FieldPriority extends Field
{
	private $actions = null;
	private $reload = false;
	private $moreActions = array();

	function __construct( $object_it = null, $reload = false, $moreActions = array() ) {
		$this->reload = $reload;
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

		$priorityIt = $this->object_it->getRef('Priority');
		echo $view->render('pm/PriorityButton.php', array (
			'data' => $priorityIt->getDisplayName(),
			'color' => $priorityIt->get('RelatedColor') == '' ? 'white' : $priorityIt->get('RelatedColor'),
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
				if ( !$this->reload ) {
					$method->setCallback( "donothing" );
				}
				else {
					$method->setCallback( "function() {window.location.reload();}" );
				}
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
