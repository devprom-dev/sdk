<?php

class TasktBoardPlanning extends TaskBoardList
{
    function getBoardAttribute() {
        return 'Release';
    }

    function getBoardAttributeFilter() {
        return 'iteration';
    }

    function buildBoardAttributeIterator()
    {
        $values = array_filter($this->getFilterValues(), function($value) {
            return !in_array($value, array('all','hide'));
        });
        $this->getTable()->parseFilterValues($values);

        $object = $this->getObject()->getAttributeObject($this->getBoardAttribute());
        $it = $object->getRegistry()->Query(
            array(
                $values['iteration'] != ''
                    ? new FilterInPredicate(preg_split('/,/', $values['iteration']))
                    : new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED),
                new FilterVpdPredicate(),
                new SortAttributeClause('StartDate.A')
            )
        );
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
            $names[$attribute_it->getId()] = $name.': '.$attribute_it->get('Caption');
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
                'Iteration'
            )
        );
    }

    function buildRelatedDataCache()
    {
        parent::buildRelatedDataCache();

        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
        $this->strategy = $methodology_it->TaskEstimationUsed() ? new EstimationHoursStrategy() : new EstimationNoneStrategy();

        if ( $this->getGroup() == 'Assignee' ) {
            $iteration_it = $this->getBoardAttributeIterator();
            $user_it = getFactory()->getObject('Participant')->getRegistry()->Query(
                array (
                    new FilterVpdPredicate(),
                    new FilterAttributePredicate('SystemUser',$this->getGroupIt()->idsToArray())
                )
            );

            while( !$user_it->end() )
            {
                $data = array();
                $this->workload[$user_it->get('SystemUser')]['Iterations'] = array();
                if ( $user_it->getId() == '' ) continue;

                while( !$iteration_it->end() )
                {
                    $data['leftwork'] = $data['leftwork'] = $iteration_it->getLeftWorkParticipant( $user_it->get('SystemUser') );
                    if ( $data['leftwork'] < 1 ) {
                        $iteration_it->moveNext();
                        continue;
                    }

                    $data['capacity'] = $iteration_it->getLeftDuration() * $user_it->get('Capacity');
                    $data['title'] = $this->strategy->getDimensionText($data['capacity']);

                    $this->workload[$user_it->get('SystemUser')]['Iterations'][$iteration_it->getId()] = $data;
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
                echo '<div class="board-header-details">';
                    echo getSession()->getLanguage()->getDateFormattedShort($object_it->get('StartDate'))
                        ." / "
                        .getSession()->getLanguage()->getDateFormattedShort($object_it->get('FinishDate'));
                    echo '<br/>';
                    if ( getSession()->getProjectIt()->getMethodologyIt()->IsAgile() ) {
                        $strategy = new EstimationHoursStrategy();
                        list( $capacity, $maximum, $actual_velocity, $estimation ) = $object_it->getRealBurndownMetrics();
                        echo sprintf(
                            text(2189),
                            $maximum > 0 ? $strategy->getDimensionText(round($maximum, 1)) : '0',
                            $estimation > $maximum ? 'label label-important' : ($maximum > 0 && $estimation < $maximum ? 'label label-success': ''),
                            $estimation > 0 ? $strategy->getDimensionText(round($estimation, 1)) : '0'
                        );
                    }
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