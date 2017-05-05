<?php

class WorkflowProgressFrame
{
    private $stateColors = array();
    private $stateNumbers = array();
    private $chartsUrl = '';

    function __construct( $object, $moduleUid )
    {
        $state_it = \WorkflowScheme::Instance()->getStateIt($object);
        while( !$state_it->end() ) {
            $this->stateColors[$state_it->get('ReferenceName')] =
                strpos($state_it->get('RelatedColor'), '#') !== false
                    ? $state_it->get('RelatedColor')
                    : '#f89406';
            $this->stateNumber[$state_it->get('ReferenceName')] = $state_it->get('OrderNum');
            $state_it->moveNext();
        }

        $module_it = getFactory()->getObject('Module')->getExact($moduleUid);
        if ( $module_it->getId() == '' ) {
            $this->chartsUrl = $moduleUid.'&view=chart&group=State';
        }
        else {
            $this->chartsUrl = $module_it->getUrl('&view=chart&group=State');
        }
    }

    function draw( $agg_it, $aggregator, $queryString = '' )
 	{
        $total = 0;
        $stateNumbers = array();
        while ( !$agg_it->end() ) {
            $value = $agg_it->get($aggregator->getAggregateAlias());
            $stateNumbers[$agg_it->get('State')] = $value;
            $total += $value;
            $agg_it->moveNext();
        }

        $states = array_flip($stateNumbers);
        usort($states, array($this, 'sortByStateAsc'));

        echo '<div class="progress" style="cursor:pointer;" onclick="javascript: window.location=\''.$this->chartsUrl.$queryString.'\';">';
        foreach( $states as $state )
 		{
            $color = $this->stateColors[$state];
            echo '<div class="bar" style="background-image:none;background-color:'.$color.';width: '.($stateNumbers[$state] / $total * 100).'%;"></div>';
            $agg_it->moveNext();
 		}
        echo '</div>';
    }

    protected function sortByStateAsc( $left, $right )
    {
        if ( $left == $right ) {
            return 1;
        }
        return $this->stateNumber[$left] > $this->stateNumber[$right] ? 1 : -1;
    }
}
