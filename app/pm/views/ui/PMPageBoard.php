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
 		$classname = $this->getBoardAttributeClassName();
 		if ( $classname == '' ) return false;
 		
 		$value_it = getFactory()->getObject($classname)->getRegistry()->Query(
 				array (
 						new FilterVpdPredicate()
 				)
 		);
 		
 		$values = array();
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

    function drawGroup($group_field, $object_it)
    {
        switch ( $group_field )
        {
            case 'Project':
                $ref_it = $this->getGroupIt();
                $ref_it->moveToId($object_it->get($group_field));

                $report_url = str_replace(
                    getSession()->getApplicationUrl(),
                    getSession()->getApplicationUrl($object_it),
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

		if ( $iterator->getId() != '' && !$this->getTable()->hasCrossProjectFilter() )
		{
			$method = new ObjectModifyWebMethod($iterator);
			if ( $method->hasAccess() ) {
				$custom_actions[] = array (
						'name' => translate('Изменить'),
						'url' => $method->getJSCall() 
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

    private $report_up_url = '';
    private $report_down_url = '';
    private $parent_it = null;
    private $report_link_drawn = false;
}
