<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

class WorkflowScheme
{
	public static function Instance()
	{
		if ( is_object(static::$singleInstance) ) return static::$singleInstance;

		$data = @file_get_contents(self::getFileName());
		if ( $data != '' ) {
			static::$singleInstance = unserialize($data);
		}
		if ( is_object(static::$singleInstance) ) return static::$singleInstance;

		return static::$singleInstance = new static();
	}

	public static function Reset() {
		static::$singleInstance = null;
	}

	public function getStateIt( $object, $state = '' ) {
		$data = $this->getStatesCache($object);
		if ( !is_array($data['states']) ) {
			return $this->getStateObject($object)->getEmptyIterator();
		}
		else {
			return $this->getStateObject($object)->createCachedIterator(
				$state == '' ? array_values($data['states']) : array($data['states'][$state])
			);
		}
	}

	public function getStates( $object ) {
		if ( array_key_exists(get_class($object), $this->stateRefNames) ) {
			return $this->stateRefNames[get_class($object)];
		}
		$data = $this->getStatesCache($object);
		return $this->stateRefNames[get_class($object)] = array_unique(
			array_map(function($row) { return $row['ReferenceName']; }, $data['states'])
		);
	}

	public function getNonTerminalStates( $object ) {
		if ( array_key_exists(get_class($object), $this->stateRefNames) ) {
			return array_diff($this->stateRefNames[get_class($object)], $this->stateRefTerminalNames[get_class($object)]);
		}
		$data = $this->getStatesCache($object);
		if ( !is_array($data['states']) ) return array();
		if ( !is_array($data['terminal']) ) return array();
		return array_diff(
			array_map(function($row) { return $row['ReferenceName']; }, $data['states']),
			array_map(function($row) { return $row['ReferenceName']; }, $data['terminal'])
		);
	}

	public function getTerminalStateIt( $object ) {
		$data = $this->getStatesCache($object);
		return $this->getStateObject($object)->createCachedIterator(array_values($data['terminal']));
	}

	public function getTerminalStates( $object ) {
		if ( array_key_exists(get_class($object), $this->stateRefTerminalNames) ) {
			return $this->stateRefTerminalNames[get_class($object)];
		}
		$data = $this->getStatesCache($object);
		if ( !is_array($data['terminal']) ) return array();
		return $this->stateRefTerminalNames[get_class($object)] = array_unique(
			array_map(function($row) { return $row['ReferenceName']; }, $data['terminal'])
		);
	}

	public function getStateAttributeIt( $object, $state = '' ) {
		$data = $this->getStatesCache($object);
		$states = $data['states'];
		if ( count($states) < 1 ) {
			return $this->stateAttribute->getEmptyIterator();
		}
		if ( $state == '' ) {
			$state = array_shift(
				array_map(function($row) { return $row['ReferenceName']; }, $data['states'])
			);
		}
		return $this->stateAttribute->createCachedIterator(
			is_array($data['stateattrs'][$state]) ? $data['stateattrs'][$state] : array()
		);
	}

	public function getStatePredicateIt( $object, $state = '' )
	{
		$data = $this->getStatesCache($object);
		$result = is_array($data['predicates']) && count($data['predicates']) > 0
			? array_values(call_user_func_array('array_merge', $data['predicates']))
			: array();
		usort($result, function( $left, $right ) {
 			return $left['Transition'] > $right['Transition'] ? 1 : -1;
		});
		return $this->rule->createCachedIterator($result);
	}

	public function getStateTransitionIt( $object, $states = array() )
	{
		$this->transition->setStateAttributeType($this->getStateObject($object));
		$data = $this->getStatesCache($object);
		if ( !is_array($states) ) {
			return $this->transition->createCachedIterator($data['transitions'][$states]);
		}
		else {
			$result = array();
			foreach( $data['transitions'] as $state => $transition ) {
				if ( in_array($state, $states) ) $result[] = $transition;
			}
			return $this->transition->createCachedIterator($result);
		}
	}

	public function getTransitionIt( $object )
	{
		$this->transition->setStateAttributeType($this->getStateObject($object));
		$data = $this->getStatesCache($object);
		return $this->transition->createCachedIterator(
			is_array($data['transitions']) && count($data['transitions']) > 0
				? array_values(call_user_func_array('array_merge', $data['transitions']))
				: array()
		);
	}

	public function invalidate() {
		@unlink($this->getFileName());
	}

 	protected function buildScheme()
 	{
		$this->cacheObjects();

        $vpds = array(
            getSession()->getProjectIt()->get('VPD')
        );
        if ( getSession() instanceof PMSession ) {
            $vpds = array_merge(
                $vpds, getSession()->getLinkedIt()->fieldToArray('VPD')
            );
        }
		$state_it = getFactory()->getObject('StateBase')->getRegistry()->Query(
			array (
				new FilterVpdPredicate(join(',',$vpds)),
				new SortOrderedClause()
			)
		);
		$stateattr_it = $this->stateAttribute->getRegistry()->Query(
			array (
				new FilterAttributePredicate('State', $state_it->idsToArray())
			)
		);
		$stateattr_it->buildPositionHash(array('State'));
		$transition_it = $this->transition->getRegistry()->Query(
			array (
				new FilterAttributePredicate('TargetState', $state_it->idsToArray()),
				new TransitionSourceStateSort()
			)
		);
		$transition_it->buildPositionHash(array('SourceState'));
		$attribute_it = getFactory()->getObject('TransitionAttribute')->getRegistry()->Query(
			array (
				new FilterAttributePredicate('Transition', $transition_it->idsToArray()),
				new SortAttributeClause('Transition')
			)
		);
		$attribute_it->buildPositionHash(array('Transition'));
		$predicate_it = getFactory()->getObject('TransitionPredicate')->getRegistry()->Query(
			array (
				new FilterAttributePredicate('Transition', $transition_it->idsToArray()),
				new SortAttributeClause('Transition')
			)
		);
		$predicate_it->buildPositionHash(array('Transition'));

		while( !$state_it->end() )
		{
			$k1 = $state_it->get('VPD');
			$k2 = $state_it->get('ObjectClass');

			$this->states[$k1][$k2]['states'][$state_it->get('ReferenceName')] = $state_it->getData();

			if ( $state_it->get('IsTerminal') == 'Y' ) {
				$this->states[$k1][$k2]['terminal'][$state_it->get('ReferenceName')] = $state_it->getData();
			}

			$stateattr_it->moveTo('State', $state_it->getId());
			while( $stateattr_it->get('State') == $state_it->getId() )
			{
				$this->states[$k1][$k2]['stateattrs'][$state_it->get('ReferenceName')][] = $stateattr_it->getData();
				$stateattr_it->moveNext();
			}

			$transition_it->moveTo('SourceState', $state_it->getId());
			while( $transition_it->get('SourceState') == $state_it->getId() )
			{
				$this->states[$k1][$k2]['transitions'][$state_it->get('ReferenceName')][] = $transition_it->getData();

				$attribute_it->moveTo('Transition', $transition_it->getId());
				while( $attribute_it->get('Transition') == $transition_it->getId() ) {
					$this->states[$k1][$k2]['attributes'][$transition_it->getId()][] = $attribute_it->getData();
					$attribute_it->moveNext();
				}

				$predicate_it->moveTo('Transition', $transition_it->getId());
				while( $predicate_it->get('Transition') == $transition_it->getId() ) {
					$this->states[$k1][$k2]['predicates'][$transition_it->getId()][] =
						array_merge(
							$predicate_it->getRef('Predicate')->getData(),
							array (
								'Transition' => $transition_it->getId()
							)
						);
					$predicate_it->moveNext();
				}

				$transition_it->moveNext();
			}

			$state_it->moveNext();
		}
 	}

	protected function getStateObject( $object ) {
		$key = get_class($object);
		if ( array_key_exists($key, $this->stateObjects) ) {
			return $this->stateObjects[$key];
		}
		$object = $object->getStateClassName() != ''
			? getFactory()->getObject($object->getStateClassName())
			: getFactory()->getObject('entity');
		return $this->stateObjects[$key] = $object;
	}

	protected function getStatesCache( $object ) {
		$vpds = $object->getVpds();
		$class = strtolower(get_class($object));
		if ( count($vpds) < 1 ) {
			return $this->states[$object->getVpdValue()][$class];
		}
		elseif ( count($vpds) == 1 ) {
			return $this->states[array_pop($vpds)][$class];
		}
		else {
			$data = array(
				'states' => array(),
				'terminal' => array(),
				'transitions' => array(),
				'stateattrs' => array(),
				'predicates' => array()
			);
			foreach( $vpds as $vpd ) {
				if ( !is_array($this->states[$vpd]) ) continue;
				foreach( array('states','transitions','terminal','stateattrs','predicates') as $type ) {
					if ( !is_array($this->states[$vpd][$class][$type]) ) continue;
					foreach( $this->states[$vpd][$class][$type] as $key => $value ) {
						foreach( $value as $valueKey => $valueItem ) {
							$data[$type][$vpd.$key][$valueKey] = $valueItem;
						}
					}
				}
			}
			return $data;
		}
	}

	protected function __construct()
	{
		$this->buildScheme();
		file_put_contents(self::getFileName(), serialize($this));
	}

	public function __sleep() {
		return array('states','stateRefNames','stateRefTerminalNames');
	}

	public function __wakeup() {
		$this->cacheObjects();
	}

	protected function cacheObjects() {
		$this->stateAttribute = getFactory()->getObject('StateAttribute');
		$this->rule = getFactory()->getObject('StateBusinessRule');
		$this->transition = getFactory()->getObject('Transition');
	}

	protected static function getFileName() {
		return CACHE_PATH.'/appcache/pm-' . getSession()->getProjectIt()->get('VPD') . "/workflow";
	}

	private $transition = null;
	private $rule = null;
	private $stateAttribute = null;
	private $stateObjects = array();
	private $states = array();
	private $stateRefNames = array();
	private $stateRefTerminalNames = array();
	private static $singleInstance = null;
}

