<?php

class FieldEstimation extends Field
{
	private $actions = null;
	private $scale = array();
	private $attribute = '';

	function __construct( $object_it = null, $attribute = '', $strategy = null )
    {
		if ( is_null($strategy) ) {
            $strategy = new EstimationHoursStrategy();
        }
        $this->object_it = $object_it;
		$this->attribute = $attribute;
		$this->scale = $strategy->getScale();
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

		$flippedScale = array_flip($this->scale);
		$mappedEstimation = $flippedScale[$this->object_it->get($this->attribute)];
		if ( $mappedEstimation == '' ) $mappedEstimation = $this->object_it->get($this->attribute);

		echo $view->render('pm/EstimationIcon.php', array (
			'data' => $mappedEstimation != '' ? $mappedEstimation : 0,
			'items' => $this->actions,
			'random' => $this->object_it->getId()
		));
	}

	protected function buildActions()
	{
		$actions = array();
        $empty_it = $this->object_it->object->getEmptyIterator();

		foreach( $this->scale as $label => $value ) {
			$method = new ModifyAttributeWebMethod($empty_it, $this->attribute, $value);
			if ( $method->hasAccess() ) {
				$actions[$value] = array(
						'name' => $label,
						'method' => $method
				);
			}
		}
		return $actions;
	}
}
