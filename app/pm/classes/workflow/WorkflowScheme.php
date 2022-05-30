<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE
include_once SERVER_ROOT_PATH . "pm/classes/project/predicates/ProjectActiveVpdPredicate.php";

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

	protected function getStateData($object)
    {
        if ( $object instanceof StatableIterator ) {
            $data = $this->states[$object->get('VPD')][$object->object->getStatableClassName()];
            $object = $object->object;
        } else {
            $data = $this->states[$object->getVpdValue()][$object->getStatableClassName()];
        }
        if ( count($data) < 1 ) {
            $data = array();
            $stateIt = $this->getStateObject($object)->getAll();
            while( !$stateIt->end() ) {
                $data['states'][$stateIt->get('ReferenceName')] = $stateIt->getData();
                if ( $stateIt->get('IsTerminal') == 'Y' ) {
                    $data['terminal'][$stateIt->get('ReferenceName')] = $stateIt->getData();
                }
                $stateIt->moveNext();
            }
        }
        return $data;
    }

	public function getStateIt( $object )
    {
        $data = $this->getStateData($object);
	    if ( $object instanceof StatableIterator ) {
            $object = $object->object;
        }
        return $this->getStateObject($object)->createCachedIterator(
            array_values($data['states'])
        );
	}

	public function getStates( $object )
    {
	    $data = $this->getStateData($object);
        return array_keys($data['states']);
	}

	public function getNonTerminalStates( $object )
    {
        $data = $this->getStateData($object);
		return array_diff(
			array_keys($data['states']),
			array_keys($data['terminal'])
		);
	}

	public function getTerminalStates( $object )
    {
        $data = $this->getStateData($object);
        return array_keys($data['terminal']);
	}

	public function getStateAttributeIt( $object, $state = '' )
    {
        $data = $this->getStateData($object);
		$states = $data['states'];
		if ( count($states) < 1 ) {
			return $this->stateAttribute->getEmptyIterator();
		}
		if ( $state == '' ) {
			$state = array_shift(
				array_map(function($row) { return $row['ReferenceName']; }, $data['states'])
			);
		}
		$stateId = $states[$state]['pm_StateId'];
		if ( $stateId < 1 ) return $this->stateAttribute->getEmptyIterator();
		return $this->stateAttribute->getRegistry()->Query(
		    array(
		        new FilterAttributePredicate('State', $stateId)
            )
        );
	}

    public function getTransitionPredicateIt( $transitionIt )
    {
        $it = $this->transitionPredicate->getRegistry()->Query(
                array(
                    new FilterAttributePredicate('Transition', $transitionIt->getId())
                )
            );
        return $this->rule->getExact($it->fieldToArray('Predicate'));
    }

	public function getStateTransitionIt( $object, $states = array() )
	{
        return $this->transition->getRegistry()->Query(
            array(
                new FilterVpdPredicate($object->getVpdValue()),
                new TransitionStateClassPredicate($object->getStatableClassName()),
                new TransitionSourceStatePredicate(join(',', $states))
            )
        );
	}

	public function getTransitionIt( $object )
	{
        return $this->transition->getRegistry()->Query(
            array(
                new FilterVpdPredicate($object->getVpdValue()),
                new TransitionStateClassPredicate($object->getStatableClassName())
            )
        );
	}

	public function invalidate() {
		@unlink($this->getFileName());
	}

 	protected function buildScheme()
 	{
		$this->cacheObjects();

		$state_it = getFactory()->getObject('StateBase')->getRegistry()->Query(
			array (
			    new ProjectActiveVpdPredicate(),
				new SortOrderedClause()
			)
		);

		while( !$state_it->end() )
		{
			$k1 = $state_it->get('VPD');
			$k2 = $state_it->get('ObjectClass');

			$this->states[$k1][$k2]['states'][$state_it->get('ReferenceName')] =
                array_filter($state_it->getData(), function ($k) { return !is_numeric($k); }, ARRAY_FILTER_USE_KEY);

			if ( $state_it->get('IsTerminal') == 'Y' ) {
				$this->states[$k1][$k2]['terminal'][$state_it->get('ReferenceName')] =
                    array_filter($state_it->getData(), function ($k) { return !is_numeric($k); }, ARRAY_FILTER_USE_KEY);
			}
			$state_it->moveNext();
		}
 	}

	protected function getStateObject( $object ) {
		$key = get_class($object);
		if ( array_key_exists($key, $this->stateObjects) ) {
			return $this->stateObjects[$key];
		}
		return $this->stateObjects[$key] = $object->getStateClassName() != ''
                    ? getFactory()->getObject($object->getStateClassName())
                    : getFactory()->getObject('entity');
	}

	protected function __construct()
	{
		$this->buildScheme();
		file_put_contents(self::getFileName(), serialize($this));
	}

	public function __sleep() {
		return array('states');
	}

	public function __wakeup() {
		$this->cacheObjects();
	}

	protected function cacheObjects() {
		$this->stateAttribute = getFactory()->getObject('StateAttribute');
		$this->rule = getFactory()->getObject('StateBusinessRule');
		$this->transition = getFactory()->getObject('Transition');
        $this->transitionPredicate = getFactory()->getObject('TransitionPredicate');
	}

	protected static function getFileName() {
		return CACHE_PATH.'/appcache/projects/' . getSession()->getProjectIt()->get('VPD') . "/workflow";
	}

	private $transition = null;
	private $transitionPredicate = null;
	private $rule = null;
	private $stateAttribute = null;
	private $stateObjects = array();
	private $states = array();
	private static $singleInstance = null;
}

