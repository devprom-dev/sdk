<?php

class ProjectDashboardList extends PMPageList
{
    function extendModel()
    {
        parent::extendModel();

        $this->milestoneBeforeDate = getSession()->getLanguage()->getPhpDate( strtotime('3 week', strtotime(date('Y-m-j'))) );

        $this->planModuleIt = getFactory()->getObject('Module')->getExact('project-plan-hierarchy');
        $this->issuesModuleIt = getFactory()->getObject('Module')->getExact('issues-backlog');
        $this->tasksModuleIt = getFactory()->getObject('Module')->getExact('tasks-list');
        $this->spentHoursIt = getFactory()->getObject('PMReport')->getExact('activitiesreport');

        $this->getObject()->addAttribute('Deadlines', 'VARCHAR', translate('Сроки'), true, false, '', 150);
        $this->getObject()->addAttribute('Progress', 'VARCHAR', translate('Прогресс'), true, false, '', 160);
    }

    function getGroupDefault() {
        return 'GroupId';
    }

    function drawCell( $object_it, $attr )
	{
		switch ( $attr )
		{
            case 'Deadlines':
                $items = array();
                $iterationIt = getFactory()->getObject('IterationActual')->getRegistry()->Query(
                    array(
                        new FilterVpdPredicate( $object_it->get('VPD') ),
                        new FilterDateBeforePredicate('StartDate', $this->milestoneBeforeDate )
                    )
                );
                while( !$iterationIt->end() ) {
                    $items[] = $this->getUidService()->getUidWithCaption($iterationIt);
                    $iterationIt->moveNext();
                }

                if ( $iterationIt->count() < 1 ) {
                    $releaseIt = getFactory()->getObject('ReleaseActual')->getRegistry()->Query(
                        array(
                            new FilterVpdPredicate( $object_it->get('VPD') ),
                            new FilterDateBeforePredicate('StartDate', $this->milestoneBeforeDate )
                        )
                    );
                    while( !$releaseIt->end() ) {
                        $items[] = $this->getUidService()->getUidWithCaption($releaseIt);
                        $releaseIt->moveNext();
                    }
                }

                $milestoneIt = getFactory()->getObject('Milestone')->getRegistry()->Query(
                    array(
                        new FilterVpdPredicate( $object_it->get('VPD') ),
                        new FilterDateBeforePredicate('MilestoneDate', $this->milestoneBeforeDate ),
                        new MilestoneActualPredicate('dummy')
                    )
                );
                while( !$milestoneIt->end() ) {
                    $items[] = $this->getUidService()->getUidWithCaption($milestoneIt);
                    $milestoneIt->moveNext();
                }

                echo join('<br/>', $items);
                if ( count($items) > 0 ) {
                    echo '<br/><a class="dashed" target="_blank" href="' . $this->planModuleIt->getUrl('', $object_it) .'">'.translate('подробнее').'</a>';
                }
                break;

            case 'Progress':
                echo '<table class="state-rich">';

                if ( $object_it->get('IssuesTotal') > 0 ) {
                    echo '<tr>';
                    echo '<td style="vertical-align: top;text-align:right;">';
                        echo translate('Пожелания');
                    echo '</td>';

                    $total = $object_it->get('IssuesTotal');
                    $completed = round($object_it->get('IssuesCompleted') / $total * 100, 0);
                    $left = round(($object_it->get('IssuesTotal') - $object_it->get('IssuesCompleted')) / $total * 100, 0);
                    echo '<td style="vertical-align: top;">';
                        echo '<div class="progress" style="cursor:pointer;" onclick="javascript: window.location=\''.$this->issuesModuleIt->getUrl('state=notresolved', $object_it).'\';">';
                        echo '<div class="bar" style="background-image:none;background-color:green;width: '.($completed).'%;"></div>';
                        echo '<div class="bar" style="background-image:none;background-color:#f89406;width: '.($left).'%;"></div>';
                    echo '</td>';
                    echo '<td style="vertical-align: top;">';
                        echo $completed . '%';
                    echo '</td>';
                    echo '</tr>';
                }

                if ( $object_it->get('TasksTotal') > 0 ) {
                    echo '<tr>';
                    echo '<td style="vertical-align: top;text-align:right;">';
                    echo translate('Задачи');
                    echo '</td>';

                    $total = $object_it->get('TasksTotal');
                    $completed = round($object_it->get('TasksCompleted') / $total * 100, 0);
                    $left = round(($object_it->get('TasksTotal') - $object_it->get('TasksCompleted')) / $total * 100, 0);
                    echo '<td style="vertical-align: top;">';
                    echo '<div class="progress" style="cursor:pointer;" onclick="javascript: window.location=\''.$this->tasksModuleIt->getUrl('state=notresolved', $object_it).'\';">';
                    echo '<div class="bar" style="background-image:none;background-color:green;width: '.($completed).'%;"></div>';
                    echo '<div class="bar" style="background-image:none;background-color:#f89406;width: '.($left).'%;"></div>';
                    echo '</td>';
                    echo '<td style="vertical-align: top;">';
                    echo $completed . '%';
                    echo '</td>';
                    echo '</tr>';
                }

                echo '</table>';
                break;

            case 'SpentHours':
            case 'SpentHoursWeek':
                parent::drawCell($object_it, $attr);
                if ( $object_it->get('VPD') != '' ) {
                    echo ' <a class="dashed" target="_blank" href="' . $this->spentHoursIt->getUrl('view=participants', $object_it) .'">'.translate('подробнее').'</a>';
                }
                break;

			default:
				parent::drawCell( $object_it, $attr );	
		}
	}

	function getItemActions($column_name, $object_it)
    {
        $actions = parent::getItemActions($column_name, $object_it);

        $actions['modify'] = array(
            'name' => translate('Открыть'),
            'click' => "javascript: window.location = '/pm/" . $object_it->get('CodeName') . "';",
            'uid' => 'modify'
        );

        return $actions;
    }

    function getIds() {
        return array();
    }
}