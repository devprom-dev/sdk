<?php
use Devprom\ProjectBundle\Service\Model\ModelService;

class PMPageBoard extends PageBoard
{
    private $backgroundColors = array();
    private $objectsPerState = array();

    function extendModel()
    {
        parent::extendModel();

        $this->projectIt = $this->buildProjectIt();
        if ( !is_object($this->projectIt) ) $this->projectIt = getSession()->getProjectIt();

        $object = getFactory()->getObject(get_class($this->getObject()));
        $predicates = array_merge(
            $this->getBoardNamesPredicates(),
            array(
                new FilterVpdPredicate($this->projectIt->fieldToArray('VPD'))
            )
        );
        foreach( $predicates as $predicate ) {
            $object->addFilter($predicate);
        }
        $object->disableVpd();

        $count_aggregate = new AggregateBase( 'State' );
        $object->addAggregate( $count_aggregate );

        $it = $object->getAggregated();
        while( !$it->end() ) {
            $this->stateObjects[$it->get('State')] = $it->get($count_aggregate->getAggregateAlias());
            $it->moveNext();
        }

        $priorityIt = getFactory()->getObject('Priority')->getAll();
        while( !$priorityIt->end() ) {
            $color = $priorityIt->get('RelatedColor');
            if ( $color != '' ) {
                $alpha = 0.12;
                $rgbData = array_map(
                    function($value) use ($alpha) {
                        return min(round($value * $alpha + 255 * (1 - $alpha), 0), 255);
                    },
                    hex2rgb($color)
                );
                $this->backgroundColors[$priorityIt->getId()] = ColorUtils::rgb2hex($rgbData);
            }
            $priorityIt->moveNext();
        }
        if ( $this->backgroundColors[1] == '' ) {
            $this->backgroundColors[1] = '#ffe1ce';
        }
        if ( $this->backgroundColors[2] == '' ) {
            $this->backgroundColors[2] = '#fcf4c7';
        }
    }

    function getBoardNamesPredicates() {
        return array();
    }

    function buildItemsCount($registry, $predicates)
    {
        $predicates = array_filter($predicates, function($predicate) {
            return ! $predicate instanceof FilterInPredicate;
        });
        $countByIt = $registry->CountBy('State', $predicates);
        while( !$countByIt->end() ) {
            $this->objectsPerState[$countByIt->get('State')] = $countByIt->get('cnt');
            $countByIt->moveNext();
        }
        return parent::buildItemsCount($registry, $predicates);
    }

	function getGroupFields()
	{
		$skip = array_merge(
            array_diff(
                $this->getObject()->getAttributesByGroup('workflow'),
                array(
                    'State'
                )
            )
        );
		return array_diff(parent::getGroupFields(), $skip );
	}

    function getGroupNullable( $field_name, $state )
    {
        switch( $field_name ) {
            case 'Project':
            case 'State':
                return false;
            default:
                return parent::getGroupNullable($field_name, $state);
        }
    }

	function hasCommonStates()
	{
        $values = array();
        $vpds = $this->getProjectIt()->fieldToArray('VPD');

 		$value_it = WorkflowScheme::Instance()->getStateIt($this->getObject());
 		while( !$value_it->end() ) {
 		    if ( in_array($value_it->get('VPD'), $vpds) ) {
                $values[$value_it->get('VPD')][] = $value_it->get('ReferenceName');
            }
 			$value_it->moveNext();
 		}

 		$example = array_shift($values);
 		foreach( $values as $attributes ) {
 			if ( count(array_diff($example, $attributes)) > 0 || count(array_diff($attributes, $example)) > 0 ) return false;
 		}

 		return true;
	}

    function getBoardNames()
    {
        $lengths = array();

        $state_it = $this->getBoardAttributeIterator();
        while( !$state_it->end() )
        {
            $ref_name = $state_it->get('ReferenceName');
            $title = $state_it->get('Caption');

            $lengths[$ref_name] += max(0, $state_it->get('QueueLength'));

            $objects = 0;
            $visibleObjects = 0;
            foreach( preg_split('/,/', $ref_name) as $stateRefName ) {
                $objects += $this->stateObjects[$stateRefName];
                $visibleObjects += $this->objectsPerState[$stateRefName];
            }

            if ( $objects != $visibleObjects ) {
                $text = text(2223);
            }
            else {
                $text = text(2224);
            }
            if ( $lengths[$ref_name] > 0 ) {
                $text = str_replace('%2', text(2507), $text);
            }

            $title .= ' '.
                str_replace('%3', $lengths[$ref_name],
                    str_replace('%1', $visibleObjects,
                        str_replace('%2', $objects,
                            str_replace(' ', '&nbsp;', $text))));

            if ( $lengths[$ref_name] > 0 && $lengths[$ref_name] < $objects ) {
                $title = '<span class="wip-o">'.$title.'</span>';
            }

            $names[$ref_name] = $title;
            $state_it->moveNext();
        }

        return $names;
    }

    function drawHeader( $board_value, $board_title )
    {
        if ( !$this->report_link_drawn && $_REQUEST['tableonly'] == '' )
        {
            echo '<div class="board-header-up">';
                echo '<div class="btn-group pull-left">';
                    echo '<button id="filter-settings" class="btn dropdown-toggle btn-xs btn-secondary">';
                        echo '<i class="icon-cog icon-white"></i>';
                    echo '</button>';
                echo '</div>';
            echo '</div>';

            echo '<div style="display:table-cell;">';
                parent::drawHeader($board_value, $board_title);
            echo '</div>';
            
            $this->report_link_drawn = true;
        }
        else {
            parent::drawHeader($board_value, $board_title);
        }
    }

    function drawCell( $object_it, $attr )
	{
		switch( $attr )
		{
			case 'State':
            	echo $this->getRenderView()->render('pm/StateColumn.php',
                    array (
                        'stateIt' => $object_it->getStateIt()
                    ));
				break;
				
			default:
                if ( $attr != 'UID' && in_array('computed', $this->object->getAttributeGroups($attr)) ) {
                    $lines = array();
                    $times = 0;
                    $result = ModelService::computeFormula($object_it, $this->object->getDefaultAttributeValue($attr));
                    foreach( $result as $computedItem ) {
                        if ( is_object($computedItem) ) {
                            if ( $times > 0 ) {
                                echo '<br/>';
                            }
                            $this->drawRefCell($computedItem, $object_it, $attr);
                            $times++;
                        }
                        else {
                            $lines[] = is_numeric($computedItem) ? round($computedItem, 1) : $computedItem;
                        }
                    }
                    if ( count($lines) > 0 ) {
                        echo join('<br/>', $lines);
                    }
                    break;
                }
				parent::drawCell( $object_it, $attr );
		}
	}

    function buildGroupIt()
    {
        switch($this->getGroup())
        {
            case 'Project':
                return $this->getProjectIt();
            case 'State':
                if ( $this->getObject() instanceof MetaobjectStatable ) {
                    $stateIt = \WorkflowScheme::Instance()->getStateIt($this->getObject());
                    $idAttribute = $this->getObject()->getIdAttribute();
                    $data = array();
                    while( !$stateIt->end() ) {
                        $data[] = array(
                            $idAttribute => $stateIt->get('ReferenceName'),
                            'Caption' => $stateIt->getDisplayName(),
                            'VPD' => $stateIt->get('VPD')
                        );
                        $stateIt->moveNext();
                    }
                    return $this->getObject()->createCachedIterator($data);
                }
                return parent::buildGroupIt();
            default:
                return parent::buildGroupIt();
        }
    }

    function getProjectIt() {
        $this->projectIt->moveFirst();
        return $this->projectIt;
    }

    function buildProjectIt()
    {
        $values = $this->getTable()->getPredicateFilterValues();

        if ( count(\TextUtils::parseFilterItems($values['target'])) < 1 ) {
            $vpd_filter = new ProjectVpdPredicate(join(',',$this->getObject()->getVpds()));
        }
        $groupFilter = in_array($values['target'],PageTable::FILTER_OPTIONS) ? '' : $values['target'];

        $registry = getFactory()->getObject('Project')->getRegistryBase();
        return $registry->Query(
            array (
                $groupFilter != ''
                    ? new FilterInPredicate(preg_split('/,/', $groupFilter))
                    : $vpd_filter
            )
        );
    }

	function getHeaderActions( $board_value )
	{
		$actions = parent::getHeaderActions($board_value);

		$custom_actions = array();
        $delete_actions = array();

		$iterator = $this->getBoardAttributeIterator();
		$iterator->moveTo('ReferenceName', $board_value);

		if ( $iterator->getId() != '' && !$iterator->object instanceof StateMeta )
		{
			$method = new ObjectModifyWebMethod($iterator);
			if ( $method->hasAccess() ) {
                $method->setRedirectUrl('function() {window.location.reload();}');
				$custom_actions[] = array (
						'name' => translate('Редактировать'),
						'url' => $method->getJSCall() 
				);
				$custom_actions[] = array();
			}

			$method = new ObjectCreateNewWebMethod($iterator->object);
			if ( $method->hasAccess() ) {
                $method->setRedirectUrl('function() {window.location.reload();}');
				$custom_actions[] = array (
						'name' => text(2011),
						'url' => $method->getJSCall(array('OrderNum' => $iterator->get('OrderNum') + 2)) 
				);
				$custom_actions[] = array (
						'name' => text(2012),
						'url' => $method->getJSCall(array('OrderNum' => max(1,$iterator->get('OrderNum') - 2))) 
				);
				$custom_actions[] = array();
			}

            $transition_actions = array();
            $transition_it = WorkflowScheme::Instance()->getStateTransitionIt($this->getObject(), array($board_value));
            while( !$transition_it->end() ) {
                $method = new ObjectModifyWebMethod($transition_it);
                if ( $method->hasAccess() ) {
                    $method->setObjectUrl($iterator->object->getPage().$transition_it->getEditUrl());
                    $transition_actions[] = array (
                        'name' => $transition_it->getDisplayName(),
                        'url' => $method->getJSCall()
                    );
                }
                $transition_it->moveNext();
            }
            if ( count($transition_actions) > 0 ) {
                $custom_actions[] = array (
                    'name' => text(2221),
                    'items' => $transition_actions
                );
                $custom_actions[] = array();
            }

            $method = new DeleteObjectWebMethod($iterator);
            if ( $method->hasAccess() ) {
                $method->setRedirectUrl('function() {window.location.reload();}');
                $delete_actions[] = array();
                $delete_actions[] = array (
                    'name' => $method->getCaption(),
                    'url' => $method->getJSCall()
                );
            }
        }

        if ( $this->getProjectIt()->count() > 1 )
        {
            $items = array();
            $widgetIt = $this->getTable()->getWidgetIt();

            $projectIt = $this->getProjectIt();
            while( !$projectIt->end() ) {
                $items[] = array(
                    'name' => $projectIt->getDisplayName(),
                    'url' => $widgetIt->getUrl('&target='.$projectIt->getId(), $projectIt)
                );
                $projectIt->moveNext();
            }

            $actions[] = array();
            $actions[] = array(
                'name' => text(2529),
                'items' => $items
            );
        }
		
		return array_merge($custom_actions, $actions, $delete_actions);
	}

    function getPriorityBackgroundColor( $priorityId ) {
        return $this->backgroundColors[$priorityId];
    }

    function getGroupEntityName( $groupField, $object_it, $referenceIt )
    {
        if ( $referenceIt->object instanceof Request ) return "";
        if ( $referenceIt->object instanceof User ) return "";
        if ( $referenceIt->object instanceof Build ) {
            return $object_it->object->getAttributeUserName($groupField);
        }
        return parent::getGroupEntityName($groupField, $object_it, $referenceIt);
    }

    private $report_link_drawn = false;
    private $stateObjects = array();
    private $projectIt = null;
}
