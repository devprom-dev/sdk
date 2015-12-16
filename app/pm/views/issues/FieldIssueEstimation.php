<?php

class FieldIssueEstimation extends Field
{
	private $actions = null;
	private $reload = false;

	function __construct( $object_it = null, $reload = false ) {
		$this->reload = $reload;
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

	function draw($view)
	{
		foreach( $this->actions as $key => $action )
		{
			$method = $action['method'];
			$this->actions[$key]['url'] = $method->getJSCall(array(), $this->object_it);
		}

		echo $view->render('pm/EstimationIcon.php', array (
				'data' => $this->object_it->get('Estimation') != '' ? $this->object_it->get('Estimation') : '0',
				'items' => $this->actions
		));
	}

	protected function buildActions()
	{
		$actions = array();
		$strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();

		foreach( $strategy->getScale() as $item )
		{
			$method = new ModifyAttributeWebMethod(getFactory()->getObject('Request')->getEmptyIterator(), 'Estimation', $item);
			if ( $method->hasAccess() ) {
				if ( !$this->reload ) {
					$method->setCallback( "donothing" );
				}
				else {
					$method->setCallback( "function() {window.location.reload();}" );
				}
				$actions[$item] = array(
						'name' => $item,
						'method' => $method
				);
			}
		}
		return $actions;
	}
}
