<?php

class RequestBoardPlanning extends RequestBoard
{
    function getBoardAttribute()
    {
        switch( $this->getTable()->getReportBase() ) {
            case 'iterationplanningboard':
                return 'Iteration';
            default:
                return 'PlannedRelease';
        }
    }

    function getBoardAttributeFilter()
    {
        switch( $this->getTable()->getReportBase() ) {
            case 'iterationplanningboard':
                return 'iteration';
            default:
                return 'release';
        }
    }

    function buildBoardAttributeIterator()
    {
        $values = array_filter($this->getTable()->getPredicateFilterValues(), function($value) {
            return !in_array($value, PageTable::FILTER_OPTIONS);
        });

        $object = $this->getObject()->getAttributeObject($this->getBoardAttribute());
        if ( $object instanceof Release ) {
            $it = $object->getRegistry()->Query(
                array(
                    $values['release'] != ''
                        ? new FilterInPredicate(preg_split('/,/', $values['release']))
                        : new ReleaseTimelinePredicate('not-passed'),
                    new FilterVpdPredicate(),
                    new FilterAttributePredicate('Project', $values['target']),
                    new SortAttributeClause('StartDate.A')
                )
            );
        }
        else {
            $it = $object->getRegistry()->Query(
                array(
                    $values['iteration'] != ''
                        ? new FilterInPredicate(preg_split('/,/', $values['iteration']))
                        : new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED),
                    new FilterVpdPredicate(),
                    new SortAttributeClause('StartDate.A')
                )
            );
        }
        return $object->createCachedIterator(
            array_merge(
                array (
                    array_merge(
                        $it->getData(),
                        array(
                            $object->getIdAttribute() => 0,
                            'Caption' => ''
                        )
                    )
                ),
                $it->getRowset()
            )
        );
    }

    function getBoardAttributeIterator()
    {
        if ( is_object($this->board_attribute_iterator) ) {
            return $this->board_attribute_iterator->copyAll();
        }
        return $this->board_attribute_iterator = $this->buildBoardAttributeIterator();
    }

    function getBoardStates() {
        return array_merge( array(''),
            array_filter(
                $this->getBoardAttributeIterator()->idsToArray(),
                function( $value ) {
                    return $value > 0;
                }
            )
        );
    }

    function getBoardNames()
    {
        $name = translate($this->getObject()->getAttributeUserName($this->getBoardAttribute()));
        $attribute_it = $this->getBoardAttributeIterator();

        $names = array();
        while ( !$attribute_it->end() )
        {
            $title = $attribute_it->get('Caption') == ''
                ? translate('Бэклог')
                : $name.': '.$attribute_it->get('Caption');

            if ( $attribute_it->getId() > 0 && $attribute_it->get('VPD') != getSession()->getProjectIt()->get('VPD') ) {
                $title = '{'.$attribute_it->get('ProjectCodeName').'} '.$title;
            }

            $names[$attribute_it->getId()] = $title;
            $attribute_it->moveNext();
        }

        return $names;
    }

    function getBoardTitles() {
        return $this->getBoardNames();
    }

    function getWatchedObjects()
    {
        return array_merge(
            parent::getWatchedObjects(),
            array (
                get_class($this->getObject()->getAttributeObject($this->getBoardAttribute()))
            )
        );
    }

    function buildRelatedDataCache()
    {
        parent::buildRelatedDataCache();

        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
        if ( $this->getBoardAttribute() == 'Iteration' ) {
            $this->strategy = $methodology_it->getIterationEstimationStrategy();
            if ( !$this->strategy instanceof EstimationHoursStrategy ) return;
        }
        else {
            $this->strategy = $methodology_it->getEstimationStrategy();
        }

        if ( $this->getGroup() != 'Owner' ) return;

        $iteration_it = $this->getBoardAttributeIterator();
        $user_it = $this->getGroupIt();
        while( !$user_it->end() )
        {
            $data = array();
            $this->workload[$user_it->getId()]['Iterations'] = array();
            if ( $user_it->getId() == '' ) continue;

            while( !$iteration_it->end() )
            {
                $request = getFactory()->getObject('pm_ChangeRequest');
                $request->addFilter( new FilterAttributePredicate($this->getBoardAttribute(), $iteration_it->getId()) );
                $request->addFilter( new FilterAttributePredicate('Owner', $user_it->getId()) );
                $request->addFilter( new StatePredicate('notterminal') );

                $data['leftwork'] = array_shift($this->strategy->getEstimation( $request, 'Estimation' ));
                if ( $data['leftwork'] < 1 ) {
                    $iteration_it->moveNext();
                    continue;
                }

                list( $capacity, $maximum, $actual_velocity ) = $iteration_it->getRealBurndownMetrics();
                $data['capacity'] = round(($capacity / $user_it->count())* $actual_velocity, 0);
                $data['title'] = $this->strategy->getDimensionText($data['capacity']);

                $this->workload[$user_it->getId()]['Iterations'][$iteration_it->getId()] = $data;
                $iteration_it->moveNext();
            }
            $iteration_it->moveFirst();
            $user_it->moveNext();
        }
    }

    function getHeaderActions( $board_value )
    {
        $actions = parent::getHeaderActions($board_value);

        if ( $board_value > 0 ) {
            $method = new ObjectModifyWebMethod(
                $this->getObject()->getAttributeObject($this->getBoardAttribute())->getExact($board_value)
            );
            if ( $method->hasAccess() ) {
                $actions = array_merge(
                    array (
                        array (
                            'name' => $method->getCaption(),
                            'url' => $method->getJSCall()
                        ),
                        array()
                    ),
                    $actions
                );
            }
        }

        return $actions;
    }

    function drawHeader( $board_value, $board_title )
    {
        if ( $board_value == array_pop($this->getBoardValues()) )
        {
            parent::drawHeader($board_value, $board_title);

            $object = $this->getObject()->getAttributeObject($this->getBoardAttribute());
            $method = new ObjectCreateNewWebMethod($object);
            if ( $method->hasAccess() ) {
                echo '<div class="board-header-op"><a class="btn btn-xs btn-success" href="'.$method->getJSCall().'"><i class="icon icon-white icon-plus"></i></a></div>';
            }
        }
        else {
            parent::drawHeader($board_value, $board_title);
        }

        if ( $board_value > 0 ) {
            $object_it = $this->getObject()->getAttributeObject($this->getBoardAttribute())->getExact($board_value);
            if ( $object_it->getId() > 0 )
            {
                $methodology_it = $object_it->getRef('Project')->getMethodologyIt();

                echo '<div class="board-header-details brd-head-details">';
                    echo $object_it->getDateFormattedShort('StartDate')
                        ." / "
                        .$object_it->getDateFormattedShort('FinishDate');
                    echo '<br/>';
                    if ( $methodology_it->IsAgile() )
                    {
                        list( $capacity, $maximum, $actual_velocity, $estimation ) = $object_it->getRealBurndownMetrics();
                        $available = $capacity * $actual_velocity;
                        echo sprintf(
                            text(2189),
                            $available > 0 ? $this->strategy->getDimensionText(round($available, 1)) : '0',
                            $estimation > $maximum ? 'label label-important' : ($maximum > 0 && $estimation < $maximum ? 'label label-success': ''),
                            $estimation > 0 ? $this->strategy->getDimensionText(round($estimation, 1)) : '0'
                        );
                    }
                echo '</div>';
            }
        }
    }

    function hasCellBasement() {
        return true;
    }

    function drawCellBasement( $boardValue, $groupValue )
    {
        parent::drawCellBasement( $boardValue, $groupValue );
        if ( trim($boardValue) == '' ) return;

        $workloadData = $this->workload[$groupValue]['Iterations'][intval($boardValue)];
        if ( is_array($workloadData) ) {
            echo $this->getRenderView()->render('pm/UserWorkloadProgress.php', array (
                'data' => $workloadData,
                'measure' => $this->strategy
            ));
        }
    }

    public function setWorkloadData( $data ) {
        $this->workload = $data;
    }

    private $board_attribute_iterator = null;
    private $workload = array();
}