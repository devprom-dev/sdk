<?php

class FieldTaskPlanned extends Field
{
	private $actions = null;

	function __construct( $object_it = null ) {
		$this->actions = $this->buildActions();
		$this->object_it = $object_it;
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
		foreach( $this->actions as $key => $action ) {
			$method = $action['method'];
			$method->setObjectIt($this->object_it);
			$this->actions[$key]['url'] = $method->getJSCall();
		}
		echo $view->render('pm/EstimationIcon.php', array (
			'data' => $this->object_it->get('Planned') != '' ? $this->object_it->get('Planned') : '0',
			'items' => $this->actions,
			'random' => $this->object_it->getId()
		));
	}

	protected function buildActions()
	{
		$actions = array();
		$empty_it = getFactory()->getObject('Task')->getEmptyIterator();
		$strategy = new EstimationHoursStrategy();

		foreach( $strategy->getScale() as $item )
		{
			$method = new ModifyAttributeWebMethod($empty_it, 'Planned', $item);
			if ( $method->hasAccess() ) {
				$method->setCallback( "donothing" );
				$actions[$item] = array(
						'name' => $item,
						'method' => $method
				);
			}
		}
		return $actions;
	}
}
