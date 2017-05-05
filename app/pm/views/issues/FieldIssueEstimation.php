<?php

class FieldIssueEstimation extends Field
{
	private $actions = null;
	private $reload = false;
	private $scale = array();

	function __construct( $object_it = null, $reload = false ) {
		$this->reload = $reload;
		$this->scale = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy()->getScale();
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
		foreach( $this->actions as $key => $action )
		{
			$method = $action['method'];
			$method->setObjectIt($this->object_it);
			$this->actions[$key]['url'] = $method->getJSCall();
		}

		$flippedScale = array_flip($this->scale);
		$mappedEstimation = $flippedScale[$this->object_it->get('Estimation')];
		if ( $mappedEstimation == '' ) $mappedEstimation = $this->object_it->get('Estimation');

		echo $view->render('pm/EstimationIcon.php', array (
			'data' => $mappedEstimation != '' ? $mappedEstimation : 0,
			'items' => $this->actions,
			'random' => $this->object_it->getId()
		));
	}

	protected function buildActions()
	{
		$actions = array();
        $empty_it = getFactory()->getObject('Request')->getEmptyIterator();

		foreach( $this->scale as $label => $value )
		{
			$method = new ModifyAttributeWebMethod($empty_it, 'Estimation', $value);
			if ( $method->hasAccess() ) {
				if ( !$this->reload ) {
					$method->setCallback( "donothing" );
				}
				else {
					$method->setCallback( "function() {window.location.reload();}" );
				}
				$actions[$value] = array(
						'name' => $label,
						'method' => $method
				);
			}
		}
		return $actions;
	}
}
