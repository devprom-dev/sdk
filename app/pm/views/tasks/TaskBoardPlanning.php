<?php

class TaskBoardPlanning extends TaskBoardList
{
    function __construct( $object ) {
        $this->strategy = new EstimationHoursStrategy();
        parent::__construct($object);
    }

    function getBoardAttribute() {
        return 'Release';
    }

    function getBoardAttributeFilter() {
        return 'iteration';
    }

    function buildBoardAttributeIterator()
    {
        $values = array_filter($this->getTable()->getPredicateFilterValues(), function($value) {
            return !in_array($value, PageTable::FILTER_OPTIONS);
        });

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
        return array_merge(array(''), $this->getBoardAttributeIterator()->idsToArray());
    }

    function getBoardNames()
    {
        $name = translate($this->getObject()->getAttributeUserName($this->getBoardAttribute()));
        $attribute_it = $this->getBoardAttributeIterator();

        $names = array();
        while ( !$attribute_it->end() )
        {
            $names[$attribute_it->getId()] = $attribute_it->get('Caption') == ''
                ? translate('Бэклог')
                : $name.': '.$attribute_it->get('Caption');
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
                'Iteration'
            )
        );
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
            if ( $object_it->getId() > 0 ) {
                echo '<div class="board-header-details brd-head-details">';
                    echo $object_it->getDateFormattedShort('StartDate') . " / " . $object_it->getDateFormattedShort('FinishDate');
                    echo '<br/>';
                    if ( getSession()->getProjectIt()->getMethodologyIt()->IsAgile() ) {
                        $available = $object_it->getParticipantsCapacity() * $object_it->getLeftDuration();
                        $maximum = $object_it->getParticipantsCapacity() * $object_it->getPlannedDurationInWorkingDays();
                        $estimation = $object_it->getTotalWorkload();
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