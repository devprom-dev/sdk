<?php

class PMPageBoard extends PageBoard
{
    function PMPageBoard( $object )
    {
        parent::PageBoard( $object );
    }

    function getReportUrl() {
        return $this->report_url;
    }

	function getGroupFields()
	{
		$skip = array_merge(
            $this->getObject()->getAttributesByGroup('trace'),
            $this->getObject()->getAttributesByGroup('workflow')
        );
		return array_diff(parent::getGroupFields(), $skip );
	}

    function getGroupNullable( $field_name ) {
        return $field_name == 'Project' ? false : parent::getGroupNullable($field_name);
    }

	function getColumnFields()
	{
		return array_merge(parent::getColumnFields(), $this->getObject()->getAttributesByGroup('workflow'));
	}
	
	function hasCommonStates()
	{
        $values = array();

 		$value_it = WorkflowScheme::Instance()->getStateIt($this->getObject());
 		while( !$value_it->end() )
 		{
 			$values[$value_it->get('VPD')][] = $value_it->get('Caption');
 			$value_it->moveNext();
 		}

 		$example = array_shift($values);
 		foreach( $values as $attributes )
 		{
 			if ( count(array_diff($example, $attributes)) > 0 || count(array_diff($attributes, $example)) > 0 ) return false;
 		}
 		
 		return true;
	}

    function drawHeader( $board_value, $board_title )
    {
        if ( $this->report_up_url != '' && $this->parent_it->getId() != '' && !$this->report_link_drawn )
        {
            $report_url = str_replace(
                getSession()->getApplicationUrl(),
                '/pm/'.$this->parent_it->get('CodeName').'/',
                $this->report_up_url
            );
            $report_url .= (strpos($report_url, '?') === false ? '?' : '&').'fitmenu';
            echo '<div class="board-header-up"><a href="'.$report_url.'" title="'.text(2099).'"><i class="icon icon-th"></i></a></div>';
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
            	echo $this->getTable()->getView()->render('pm/StateColumn.php', array (
									'color' => $object_it->get('StateColor'),
									'name' => $object_it->get('StateName'),
									'terminal' => $object_it->get('StateTerminal') == 'Y'
							));
				break;
				
			default:
				parent::drawCell( $object_it, $attr );
		}
	}

    function getGroupIt()
    {
        foreach( $this->getTable()->getFilterPredicates() as $filter ) {
            if ( $filter instanceof FilterVpdPredicate ) {
                $vpd_filter = $filter;
            }
        }
        if ( !is_object($vpd_filter) ) {
            $vpd_filter = new FilterVpdPredicate(join(',',$this->getObject()->getVpds()));
        }

        switch($this->getGroup())
        {
            case 'Project':
                $values = $this->getFilterValues();
                $groupFilter = in_array($values['target'],array('all','none','hide')) ? '' : $values['target'];

                return getFactory()->getObject('Project')->getRegistry()->Query(
                    array (
                        $groupFilter != ''
                            ? new FilterInPredicate(preg_split('/,/', $groupFilter))
                            : $vpd_filter
                    )
                );
            default:
                return parent::getGroupIt();
        }
    }

    function drawGroup($group_field, $object_it)
    {
        switch ( $group_field )
        {
            case 'Project':
                $ref_it = $object_it->getRef($group_field);

                $report_url = str_replace(
                    getSession()->getApplicationUrl(),
                    getSession()->getApplicationUrl($ref_it),
                    $this->report_down_url
                );
                $report_url .= (strpos($report_url, '?') === false ? '?' : '&').'fitmenu';
                echo '<i class="icon icon-th"></i><a class="btn btn-link" href="'.$report_url.'">'.$ref_it->getDisplayName().'</a>';
                break;

            default:
                parent::drawGroup($group_field, $object_it);
        }
    }

	function getHeaderActions( $board_value )
	{
		$actions = parent::getHeaderActions($board_value);

		$custom_actions = array();
		
		$iterator = $this->getBoardAttributeIterator();
		$iterator->moveTo('ReferenceName', $board_value);

		if ( $iterator->getId() != '' && !getSession()->getProjectIt()->IsPortfolio() )
		{
			$method = new ObjectModifyWebMethod($iterator);
			if ( $method->hasAccess() ) {
				$custom_actions[] = array (
						'name' => translate('Изменить'),
						'url' => $method->getJSCall() 
				);
				$custom_actions[] = array();
			}

            $transition_actions = array();
            $transition_it = WorkflowScheme::Instance()->getStateTransitionIt($this->getObject(), $board_value);
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
                    'name' => translate('Переходы'),
                    'items' => $transition_actions
                );
                $custom_actions[] = array();
            }

			$method = new ObjectCreateNewWebMethod($iterator->object);
			if ( $method->hasAccess() ) {
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
		}
		
		return array_merge($custom_actions, $actions);
	}

    function getRenderParms()
    {
        $reports_map = array (
            'issuesboard' => 'issues/board/issuesboardcrossproject',
            'issues-board' => 'issues/board/issuesboardcrossproject',
            'tasksboard' => 'tasks/board/tasksboardcrossproject',
            'tasks-board' => 'tasks/board/tasksboardcrossproject'
        );
        $rev_reports_map = array (
            'issuesboardcrossproject' => 'issues/board/issuesboard',
            'tasksboardcrossproject' => 'tasks/board/tasksboard'
        );

        $report_id = $this->getTable()->getReport();

        if ( $report_id != '' ) {
            $report_it = getFactory()->getObject('PMReport')->getExact($report_id);
            if (is_numeric($report_id)) {
                $report_id = $report_it->get('Report') != '' ? $report_it->get('Report') : $report_it->get('Module');
            }
        }

        if ( $report_id == '' ) {
            $report_id = $this->getTable()->getPage()->getModule();
            if ( $report_id != '' ) {
                $report_it = getFactory()->getObject('Module')->getExact($report_id);
            }
        }

        if ( $report_id != '' ) {
            $report_up_id = $reports_map[$report_id];
            if ( $report_up_id == '' ) {
                $this->report_up_url = $report_it->getUrl();
            }
            else {
                $this->report_up_url = getSession()->getApplicationUrl().$report_up_id;
            }
            $report_down_id = $rev_reports_map[$report_id];
            if ( $report_down_id == '' ) {
                $this->report_down_url = $report_it->getUrl();
            }
            else {
                $this->report_down_url = getSession()->getApplicationUrl().$report_down_id;
            }
        }

        if ( $reports_map[$report_id] != '' && getSession()->getProjectIt()->IsProgram() ) {
            $this->parent_it = getSession()->getProjectIt();
        }
        else {
            $this->parent_it = getSession()->getProjectIt()->getParentIt();
            if ( $this->parent_it->getId() == '' && getSession()->getProjectIt()->get('CodeName') != 'all' ) {
                $portfolio_it = getFactory()->getObject('Portfolio')->getAll();
                $portfolio_it->moveTo('CodeName', 'all');
                $this->parent_it = $portfolio_it;
            }
        }

        return parent::getRenderParms();
    }

    function buildFilterActions( & $base_actions )
    {
        parent::buildFilterActions( $base_actions );
        $this->buildFilterColumnsGroup( $base_actions, 'workflow' );
        $this->buildFilterColumnsGroup( $base_actions, 'trace' );
        $this->buildFilterColumnsGroup( $base_actions, 'time' );
        $this->buildFilterColumnsGroup( $base_actions, 'dates' );
    }

    private $report_up_url = '';
    private $report_down_url = '';
    private $parent_it = null;
    private $report_link_drawn = false;
}
