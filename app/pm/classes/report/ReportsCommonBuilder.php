<?php

include_once SERVER_ROOT_PATH."pm/classes/report/ReportsBuilder.php";

class ReportsCommonBuilder extends ReportsBuilder
{
    public function build( ReportRegistry & $object )
    {
        $areas = getFactory()->getObject('ModuleCategory')->getAll()->fieldToArray('ReferenceName');
        $qa_area = in_array('qa', $areas) ? 'qa' : FUNC_AREA_MANAGEMENT;

        $module = getFactory()->getObject('Module');
		$request = getFactory()->getObject('pm_ChangeRequest');

		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		$separateIssues = getSession()->IsRDD();
		$project_it = getSession()->getProjectIt();

        $terminal = array('Y');
        $nonterminal = array('N','I');

		$module_it = $module->getExact('issues-backlog');
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
			$object->addReport(
				array ( 'name' => 'allissues',
					'title' => $separateIssues ? text(2652) : text(801),
					'description' => text(1398),
					'category' => FUNC_AREA_MANAGEMENT,
					'query' => 'state=all',
					'module' => $module_it->getId() )
			);

            $object->addReport(
                array ( 'name' => 'readyissues',
                    'title' => $separateIssues ? text(2653) : text(2509),
                    'description' => text(1399),
                    'category' => FUNC_AREA_MANAGEMENT,
                    'query' => 'state='.join($terminal,',').'&version=all',
                    'module' => $module_it->getId() )
            );

			$object->addReport(
				array ( 'name' => 'productbacklog',
						'description' => text(819),
						'category' => FUNC_AREA_MANAGEMENT,
				        'query' => 'state='.array_shift(array_values(\WorkflowScheme::Instance()->getNonTerminalStates($request))),
				        'module' => $module_it->getId() )
			);

            $object->addReport( array (
                'name' => 'issuesestimation',
                'title' => $separateIssues ? text(2659) : text(2290),
                'description' => text(2669),
                'category' => FUNC_AREA_MANAGEMENT,
                'query' => 'state=all&group=Function',
                'module' => $module_it->getId()
            ));

            $object->addReport( array(
                'name' => 'assignedissues',
                'title' => text(3115),
                'query' => 'state='.join(',',$nonterminal),
                'category' => FUNC_AREA_MANAGEMENT,
                'module' => $module_it->getId()
                ));
        }
		
 		$module_it = $module->getExact('issues-board');
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
            if ( $project_it->IsProgram() || $project_it->IsSubproject() || $project_it->IsPortfolio() ) {
                if ( getFactory()->getObject('SharedObjectSet')->sharedInProject(new Request(), $project_it) ) {
                    $object->addReport( array (
                        'name' => 'commonissuesboard',
                        'title' => text(3127),
                        'category' => FUNC_AREA_MANAGEMENT,
                        'query' => 'group=Project',
                        'module' => $module_it->getId()
                    ));
                }
            }

            if ( !$project_it->IsPortfolio() ) {
                if ( $project_it->getMethodologyIt()->get('IsKanbanUsed') != 'Y' ) {
                    $object->addReport(
                        array ( 'name' => 'issuesboard',
                            'category' => FUNC_AREA_MANAGEMENT,
                            'module' => $module_it->getId() )
                    );
                }
            }

            if ( $methodology_it->HasReleases() ) {
                $object->addReport(
                    array (
                        'name' => 'releaseplanningboard',
                        'title' => text(1347),
                        'description' => text(1411),
                        'category' => FUNC_AREA_MANAGEMENT,
                        'query' => ($methodology_it->HasTasks() ? 'group=none' : 'group=Owner').'&state='.join(',',$nonterminal),
                        'module' => $module_it->getId() )
                );
            }
            if ( $methodology_it->HasPlanning() ) {
                $object->addReport(
                    array (
                        'name' => 'iterationplanningboard',
                        'title' => text(2190),
                        'description' => text(2667),
                        'category' => FUNC_AREA_MANAGEMENT,
                        'query' => ($methodology_it->HasTasks() ? 'group=none' : 'group=Owner').'&state='.join(',',$nonterminal),
                        'module' => $module_it->getId() )
                );
            }

			$object->addReport(
				array ( 'name' => 'issuesboarddeadlines',
						'title' => $separateIssues ? text(2654) : text(1939),
				        'description' => text(1940),
						'category' => FUNC_AREA_MANAGEMENT,
						'query' => 'group=DueWeeks&state='.join(',',$nonterminal),
				        'module' => $module_it->getId() )
			);
		}
		
		$issues_chart_it = $module->getExact('issues-chart');
		if ( getFactory()->getAccessPolicy()->can_read($issues_chart_it) )
		{
			$object->addReport(
				array ( 'name' => 'issuesimplementationchart',
						'title' => $separateIssues ? text(2655) : text(995),
						'description' => text(1019),
				        'query' => 'group=history&aggby=State&state='.join(',',$nonterminal).'&modifiedafter=last-month',
						'category' => FUNC_AREA_MANAGEMENT,
						'type' => 'chart',
				        'module' => $issues_chart_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'issuesbyprioritieschart',
						'title' => $separateIssues ? text(2656) : text(997),
				        'description' => text(1415),
				        'query' => 'group=history&aggby=Priority&state=all&modifiedafter=last-month',
				        'category' => $qa_area,
						'type' => 'chart',
				        'module' => $issues_chart_it->getId() )
			);

			if ( $methodology_it->IsAgile() && $methodology_it->HasReleases() )
			{
				$object->addReport(
					array ( 'name' => 'releaseburndown',
							'title' => text(1196),
					        'description' => text(1396),
					        'query' => 'chartdata=hide&chartlegend=hide',
					        'category' => FUNC_AREA_MANAGEMENT,
							'type' => 'chart',
							'icon' => 'icon-fire',
					        'module' => $issues_chart_it->getId() )
				);
			}
			
			if ( $methodology_it->IsAgile() && $methodology_it->HasPlanning() )
			{
                $object->addReport(
                    array ( 'name' => 'projectburnup',
                        'title' => text(1928),
                        'query' => 'chartdata=hide&chartlegend=hide',
                        'category' => FUNC_AREA_MANAGEMENT,
                        'type' => 'chart',
                        'icon' => 'icon-fire',
                        'module' => $issues_chart_it->getId() )
                );
			}
			
            $object->addReport(
                array ( 'name' => 'issuesbyownerschart',
                        'title' => $separateIssues ? text(2657) : text(999),
                        'query' => 'group=Owner&state=all&aggby=Priority&aggregator=none&modifiedafter=last-month',
                        'category' => FUNC_AREA_MANAGEMENT,
                        'type' => 'chart',
                        'description' => text(1205),
                        'module' => $issues_chart_it->getId() )
            );
		}

		$issues_trace_it = $module->getExact('issues-trace');
		if ( getFactory()->getAccessPolicy()->can_read($issues_trace_it) )
		{
			$object->addReport(
				array ( 'name' => 'issues-trace',
						'description' => text(1015),
				        'query' => 'group=PlannedRelease&state=all&sort=PlannedRelease.D&sort2=RecordModified.D',
				        'category' => FUNC_AREA_MANAGEMENT,
				        'module' => $issues_trace_it->getId() )
			);
		}

    	$task = getFactory()->getObject('pm_Task');

        if ( $project_it->IsPortfolio() ) {
            $terminal_states = array('Y');
            $non_terminal_states = array('N','I');
        }
        else {
            $terminal_states = array_merge(
                $task->getTerminalStates(),
                array('Y')
            );
            $non_terminal_states = array_merge(
                $task->getNonTerminalStates(),
                array('N','I')
            );
        }

		$task_list_it = $module->getExact('tasks-list');
		if ( getFactory()->getAccessPolicy()->can_read($task_list_it) )
		{
            if ( $methodology_it->HasTasks() ) {
                $query_common = '';
                if ( $methodology_it->HasPlanning() ) {
                    $query_common .= '&group=Release';
                }

                $object->addReport(
                    array ( 'name' => 'assignedtasks',
                        'title' => text(3110),
                        'description' => text(3111),
                        'query' => $query_common.'&taskassignee=user-id&iteration=all&state='.join(',',$non_terminal_states),
                        'category' => FUNC_AREA_MANAGEMENT,
                        'module' => $task_list_it->getId() )
                );

                $object->addReport(
                    array ( 'name' => 'currenttasks',
                        'title' => text(1356),
                        'description' => text(1417),
                        'query' => $query_common.'&iteration=all&state='.join(',',$non_terminal_states),
                        'category' => FUNC_AREA_MANAGEMENT,
                        'module' => $task_list_it->getId() )
                );

                $object->addReport(
                    array ( 'name' => 'resolvedtasks',
                        'title' => text(531),
                        'description' => text(1416),
                        'query' => 'iteration=all&state='.join(',',$terminal_states),
                        'category' => FUNC_AREA_MANAGEMENT,
                        'module' => $task_list_it->getId() )
                );

                $object->addReport(
                    array ( 'name' => 'tasksefforts',
                        'title' => text(2535),
                        'description' => text(2670),
                        'query' => 'iteration=all&state=all',
                        'category' => FUNC_AREA_MANAGEMENT,
                        'module' => $task_list_it->getId() )
                );
            }

            $object->addReport( array (
                    'name' => 'mytasks',
                    'title' => text(3112),
                    'description' => text(1406),
                    'query' => 'group=Release&taskassignee=user-id',
                    'category' => FUNC_AREA_MANAGEMENT,
                    'module' => $task_list_it->getId() )
            );
            $object->addReport( array (
                    'name' => 'tasksbyassignee',
                    'title' => text(2303),
                    'description' => text(2668),
                    'query' => 'group=Assignee',
                    'category' => FUNC_AREA_MANAGEMENT,
                    'module' => $task_list_it->getId() )
            );
            $object->addReport( array (
                'name' => 'nearesttasks',
                'title' => text(2476),
                'query' => 'plannedfinish=next-week',
                'category' => FUNC_AREA_MANAGEMENT,
                'description' => text(2664),
                'module' => $task_list_it->getId() )
            );
            $object->addReport(
                array ( 'name' => 'newtasks',
                    'title' => text(2138),
                    'query' => 'submittedon=last-week',
                    'category' => FUNC_AREA_MANAGEMENT,
                    'description' => text(2666),
                    'module' => $task_list_it->getId() )
            );
            $object->addReport(
                array ( 'name' => 'issuesmine',
                    'title' => text(751),
                    'description' => text(1404),
                    'category' => FUNC_AREA_MANAGEMENT,
                    'query' => 'author=user-id&sort=RecordCreated.D',
                    'module' => $task_list_it->getId() )
            );
            $object->addReport(
                array (
                    'name' => 'watchedtasks',
                    'title' => text(2310),
                    'category' => FUNC_AREA_MANAGEMENT,
                    'query' => 'watcher=user-id',
                    'description' => text(2665),
                    'module' => $task_list_it->getId()
                )
            );
		}

		$task_chart_it = $module->getExact('tasks-board');
        if ( $methodology_it->HasTasks() ) {
            if ( getFactory()->getAccessPolicy()->can_read($task_chart_it) ) {
                $object->addReport(
                    array (
                        'name' => 'tasksboard',
                        'category' => FUNC_AREA_MANAGEMENT,
                        'module' => $task_chart_it->getId() )
                );
                if ( getFactory()->getAccessPolicy()->can_create($request) ) {
                    $object->addReport(
                        array(
                            'name' => 'tasksboardforissues',
                            'title' => text(2222),
                            'query' => 'group=ChangeRequest',
                            'category' => FUNC_AREA_MANAGEMENT,
                            'module' => $task_chart_it->getId())
                    );
                }
                if ( count($task->getVpds()) == 1 && $methodology_it->HasPlanning() && $methodology_it->UsePlanningByTasks() ) {
                    $object->addReport(
                        array (
                            'name' => 'tasksplanningboard',
                            'title' => text(1348),
                            'description' => text(1410),
                            'query' => 'group=Assignee&iteration=all&state=' . join(',', $non_terminal_states),
                            'category' => FUNC_AREA_MANAGEMENT,
                            'module' => $task_chart_it->getId() )
                    );
                }
            }
        }

 	    $task_chart_it = $module->getExact('tasks-trace');
        if ( $methodology_it->HasTasks() && getFactory()->getAccessPolicy()->can_read($task_chart_it) )
        {
			$object->addReport(
				array ( 'name' => 'tasks-trace',
				        'description' => text(1391),
						'query' => 'group=Release&state=all&sort=Release.D&sort2=RecordModified.D',
				        'category' => FUNC_AREA_MANAGEMENT,
				        'module' => $task_chart_it->getId() )
			);
        }
        
        $task_chart_it = $module->getExact('tasks-chart');
        if ( $methodology_it->HasTasks() && getFactory()->getAccessPolicy()->can_read($task_chart_it) )
        {
			$object->addReport(
				array ( 'name' => 'tasksimplementationchart',
						'title' => text(1006),
				        'description' => text(1400),
						'query' => 'group=history&aggby=State&state=all&modifiedafter=last-month',
				        'category' => FUNC_AREA_MANAGEMENT,
						'type' => 'chart',
				        'module' => $task_chart_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'tasksbyprioritieschart',
						'title' => text(1007),
				        'description' => text(1414),
						'query' => 'group=history&aggby=Priority&state=all&modifiedafter=last-month',
				        'category' => FUNC_AREA_MANAGEMENT,
						'type' => 'chart',
				        'module' => $task_chart_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'tasksbyassigneeschart',
						'title' => text(1008),
				        'description' => text(1413),
						'query' => 'group=Assignee&state=all&aggby=Priority&aggregator=none&modifiedafter=last-month',
				        'category' => FUNC_AREA_MANAGEMENT,
						'type' => 'chart',
				        'module' => $task_chart_it->getId() )
			);			
		
			$object->addReport(
				array ( 'name' => 'tasksreopenedbyassigneechart',
						'title' => text(1888),
				        'description' => text(1889),
				        'query' => 'group=Assignee&aggby=Priority&aggregator=none&state=all&was-transition='.join(',',$this->getReopenTransitions($task)),
				        'category' => FUNC_AREA_MANAGEMENT,
						'type' => 'chart',
				        'module' => $task_chart_it->getId() )
			);
			
			$object->addReport(
				array ( 'name' => 'tasksplanbytypes',
						'title' => text(1109),
				        'description' => text(1412),
						'query' => 'aggregator=SUM&group=TaskType&aggby=Planned&state=all&modifiedafter=last-month',
				        'category' => FUNC_AREA_MANAGEMENT,
						'type' => 'chart',
				        'module' => $task_chart_it->getId() )
			);

			$object->addReport(
				array ( 'name' => 'tasksfactbytypes',
						'title' => text(1110),
				        'description' => text(1419),
						'query' => 'aggregator=SUM&group=TaskType&aggby=Fact&state=all&modifiedafter=last-month',
				        'category' => FUNC_AREA_MANAGEMENT,
						'type' => 'chart',
				        'module' => $task_chart_it->getId() )
			);

			if ( $methodology_it->TaskEstimationUsed() )
			{
				$object->addReport(
					array(
						'name' => 'tasksplanbyfact',
						'title' => text(2063),
						'category' => FUNC_AREA_MANAGEMENT,
						'query' => 'state=' . join(',', $terminal_states) . '&modifiedafter=last-month',
						'type' => 'chart',
						'module' => $task_chart_it->getId()
					)
				);
			}

			if ( $methodology_it->IsAgile() && $methodology_it->HasPlanning() )
			{
				$object->addReport(
					array ( 'name' => 'iterationburndown',
							'title' => text(1195),
					        'description' => text(1397),
							'query' => 'chartdata=hide&chartlegend=hide',
					        'category' => FUNC_AREA_MANAGEMENT,
							'type' => 'chart',
							'icon' => 'icon-fire',
					        'module' => $task_chart_it->getId() )
				);
			}
        }

   		$object->addReport( array (
   		    'name' => 'navigation-settings',
            'title' => text(1326),
            'description' => text(3009),
            'category' => FunctionalAreaMenuSettingsBuilder::AREA_UID,
            'module' => $module->getExact('menu')->getId()
        ));
		
		$module_it = $module->getExact('project-knowledgebase');
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
    		$object->addReport( array(
    		    'name' => 'project-knowledgebase',
                'description' => text(1700),
                'category' => FUNC_AREA_MANAGEMENT,
                'module' => $module_it->getId()
            ));

            $object->addReport( array(
                'name' => 'knowledgebaselist',
                'title' => text(1372),
                'category' => FUNC_AREA_MANAGEMENT,
                'query' => 'view=list',
                'module' => $module_it->getId()
            ));
		}
		
		$log_it = $module->getExact('project-log');
		
		if ( getFactory()->getAccessPolicy()->can_read($log_it) )
		{
    		$object->addReport(
    		        array ( 'name' => 'project-log',
    		                'description' => text(1266),
						    'query' => 'start=last-week',
    		                'category' => FUNC_AREA_MANAGEMENT,
    		                'module' => $log_it->getId() 
    	    ));
    		
    		$object->addReport(
    		        array ( 'name' => 'discussions',
    		                'title' => text(2807),
    		                'description' => text(1409),
						    'query' => 'action=commented&start=last-week',
    		                'category' => FUNC_AREA_MANAGEMENT,
		                    'module' => $log_it->getId(),
							'icon' => 'icon-comment'
    		));
		}
		
		$module_it = $module->getExact('project-spenttime');
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
            if ( getFactory()->getAccessPolicy()->can_read($request) ) {
                $object->addReport(array(
                    'name' => 'activitiesreport',
                    'category' => FUNC_AREA_MANAGEMENT,
                    'module' => $module_it->getId()
                ));

                if ( getSession()->IsRDD() ) {
                    $object->addReport(array(
                        'name' => 'activitiesreportincrements',
                        'title' => text(2912),
                        'category' => FUNC_AREA_MANAGEMENT,
                        'module' => $module_it->getId()
                    ));
                }
            }

            $object->addReport( array (
                'name' => 'activitiesreportproject',
                'title' => text(2909),
                'category' => FUNC_AREA_MANAGEMENT,
                'module' => $module_it->getId()
            ));

            if ( $methodology_it->HasTasks() && getFactory()->getAccessPolicy()->can_read($task) ) {
                $object->addReport( array (
                    'name' => 'activitiesreporttasks',
                    'title' => text(2910),
                    'category' => FUNC_AREA_MANAGEMENT,
                    'module' => $module_it->getId()
                ));
            }

            $object->addReport( array (
                'name' => 'activitiesreportusers',
                'title' => text(2911),
                'category' => FUNC_AREA_MANAGEMENT,
                'module' => $module_it->getId()
            ));
		}

        $module_it = $module->getExact('worklog-chart');
        if ( getFactory()->getAccessPolicy()->can_read($module_it) )
        {
            $object->addReport(
                array (
                    'name' => 'activitieschart',
                    'title' => text(2492),
                    'description' => text(2671),
                    'category' => FUNC_AREA_MANAGEMENT,
                    'module' => $module_it->getId(),
                    'type' => 'chart',
                    'query' => 'view=chart&projectuser=all&group=Participant&aggby=Capacity&aggregator=SUM',
                )
            );
        }

    	$module_it = $module->getExact('project-plan-hierarchy');
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
			$object->addReport(
				array ( 'name' => 'projectplan',
						'title' => text(3120),
				        'description' => text(1389),
						'category' => FUNC_AREA_MANAGEMENT,
				        'module' => $module_it->getId() )
			);
		}
		
		$module_it = $module->getExact('attachments');
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
			$object->addReport(
					array ( 'name' => 'attachments',
							'category' => FUNC_AREA_MANAGEMENT,
							'module' => $module_it->getId() )
			);
		}

        $module_it = $module->getExact('project-reports');
        if ( getFactory()->getAccessPolicy()->can_read($module_it) )
        {
            $object->addReport( array (
                'name' => 'charts',
                'title' => text(2229),
                'query' => 'reporttype=chart',
                'icon' => 'icon-signal',
                'category' => FUNC_AREA_MANAGEMENT,
                'module' => $module_it->getId()
            ));
        }

        $module_it = $module->getExact('project-question');
        if ( getFactory()->getAccessPolicy()->can_read($module_it) )
        {
            $object->addReport( array (
                'name' => 'closed-discussions',
                'title' => text(2806),
                'query' => 'state=resolved,Y',
                'icon' => 'icon-question',
                'category' => FUNC_AREA_MANAGEMENT,
                'module' => $module_it->getId()
            ));
        }
    }
    
    protected function getReopenTransitions( $object )
    {
		return WorkflowScheme::Instance()->getStateTransitionIt(
			$object, WorkflowScheme::Instance()->getTerminalStates($object)
		)->idsToArray();
    }
}