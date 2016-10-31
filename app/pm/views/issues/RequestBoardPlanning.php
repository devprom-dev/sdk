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

    function buildBoardAttributeIterator()
    {
        $object = $this->getObject()->getAttributeObject($this->getBoardAttribute());
        if ( $object instanceof Release ) {
            $it = $object->getRegistry()->Query(
                array(
                    new ReleaseTimelinePredicate('not-passed'),
                    new FilterVpdPredicate(),
                    new SortAttributeClause('StartDate.A')
                )
            );
        }
        else {
            $it = $object->getRegistry()->Query(
                array(
                    new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED),
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
                            'Caption' => translate('<нет значения>')
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
        return array_merge(array(''), $this->getBoardAttributeIterator()->idsToArray());
    }

    function getBoardNames()
    {
        $name = translate($this->getObject()->getAttributeUserName($this->getBoardAttribute()));
        $attribute_it = $this->getBoardAttributeIterator();

        $names = array();
        while ( !$attribute_it->end() )
        {
            $title = $name.': '.$attribute_it->get('Caption');
            $names[$attribute_it->getId()] = $title;
            $attribute_it->moveNext();
        }

        return $names;
    }

    function getBoardTitles() {
        return array_values(array_unique($this->getBoardAttributeIterator()->fieldToArray('Caption')));
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
        $this->strategy = $methodology_it->getEstimationStrategy();

        if ( $this->getBoardAttribute() == 'PlannedRelease' && $this->getGroup() == 'Owner' ) {
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
                    $request->addFilter( new FilterAttributePredicate('PlannedRelease', $iteration_it->getId()) );
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
    }

    function getHeaderActions( $board_value )
    {
        $actions = parent::getHeaderActions($board_value);

        if ( $board_value > 0 ) {
            $method = new ObjectModifyWebMethod(
                $this->getObject()->getAttributeObject($this->getBoardAttribute())->getExact($board_value)
            );
            if ( $method->hasAccess() ) {
                $method->setRedirectUrl('donothing');
                $actions = array_merge(
                    array (
                        array (
                            'name' => translate('Изменить'),
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
        echo '<div>';
            if ( $board_value == array_pop($this->getBoardValues()) )
            {
                echo '<div style="display:table-cell;">';
                    parent::drawHeader($board_value, $board_title);
                echo '</div>';
                $object = $this->getObject()->getAttributeObject($this->getBoardAttribute());
                $method = new ObjectCreateNewWebMethod($object);
                if ( $method->hasAccess() ) {
                    echo '<div class="board-header-op"><a class="btn btn-mini btn-success" href="'.$method->getJSCall().'"><i class="icon icon-white icon-plus"></i></a></div>';
                }
            }
            else {
                parent::drawHeader($board_value, $board_title);
            }
        echo '</div>';
        if ( $board_value > 0 ) {
            $object_it = $this->getObject()->getAttributeObject($this->getBoardAttribute())->getExact($board_value);
            if ( $object_it->getId() > 0 ) {
                $strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
                if ( $object_it->object instanceof Iteration ) {
                    $estimation = $object_it->getLeftEstimation();
                    list( $capacity, $maximum, $actual_velocity ) = $object_it->getEstimationRealBurndownMetrics();
                }
                else {
                    $estimation = $object_it->getTotalWorkload();
                    list( $capacity, $maximum, $actual_velocity ) = $object_it->getRealBurndownMetrics();
                }
                echo '<div class="board-header-details">';
                    echo getSession()->getLanguage()->getDateFormattedShort($object_it->get('StartDate'))
                        ." / "
                        .getSession()->getLanguage()->getDateFormattedShort($object_it->get('FinishDate'));
                    echo '<br/>';
                    echo sprintf(
                        text(2189),
                        $strategy->getDimensionText(round($maximum, 1)),
                        $estimation > $maximum ? 'label label-important' : ($maximum > 0 && $estimation < $maximum ? 'label label-success': ''),
                        $strategy->getDimensionText(round($estimation, 1))
                    );
                echo '</div>';
            }
        }
    }

    function drawCellBasement( $boardValue, $groupValue )
    {
        parent::drawCellBasement( $boardValue, $groupValue );

        $workloadData = $this->workload[$groupValue]['Iterations'][intval($boardValue)];
        if ( is_array($workloadData) ) {
            echo $this->getTable()->getView()->render('pm/UserWorkloadDetails.php', array (
                'data' => array( 'Iterations' => array($workloadData) ),
                'measure' => trim($this->strategy->getDimensionText(''))
            ));
        }
    }

    private $board_attribute_iterator = null;
}