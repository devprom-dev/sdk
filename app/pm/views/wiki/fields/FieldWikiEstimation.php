<?php
include_once SERVER_ROOT_PATH . "pm/classes/settings/EstimationHoursStrategy.php";

class FieldWikiEstimation extends Field
{
	private $actions = null;
	private $object_it = null;
	private $scale = array();

	function __construct( $object )
    {
        $this->object = $object;
        $strategy = new EstimationHoursStrategy();
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
        $empty_it = $this->object->getEmptyIterator();

		foreach( $this->scale as $label => $value )
		{
			$method = new ModifyAttributeWebMethod($empty_it, 'Estimation', $value);
			if ( $method->hasAccess() ) {
                $method->setCallback( "donothing" );
				$actions[$value] = array(
						'name' => $label . ' ' . translate('ч.'),
						'method' => $method
				);
			}
		}
		return $actions;
	}
}
