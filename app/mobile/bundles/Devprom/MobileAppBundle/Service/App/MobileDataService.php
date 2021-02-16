<?php
namespace Devprom\MobileAppBundle\Service\App;
include_once SERVER_ROOT_PATH.'pm/classes/workflow/WorkflowModelBuilder.php';

class MobileDataService
{
    private $lastItem = 0;
    private $maxItems = 0;
    private $userIt = null;

    function __construct( $lastItem = 0, $maxItems = 10 ) {
        $this->lastItem = $lastItem;
        $this->maxItems = $maxItems;
        $this->userIt = getSession()->getUserIt();
    }

    public function getWhatsNewCards()
    {
        $log = getFactory()->getObject('ChangeLogWhatsNew');
        $logRegistry = $this->getRegistry($log);

        $data = array();
        $logIt = $logRegistry->Query(
            array(
                new \ChangeLogSinceNotificationFilter($this->userIt),
                new \ChangeLogVisibilityFilter(),
                new \FilterAttributeNotNullPredicate('Caption'),
                new \FilterVpdPredicate(),
                new \SortChangeLogRecentClause()
            )
        );

        $item = 0;
        while( !$logIt->end() && $item++ < $this->maxItems )
        {
            $anchorIt = $logIt->getObjectIt();
            $data[] = array(
                'id' => $this->lastItem + $item,
                'title' => is_object($anchorIt) ? $anchorIt->getHtmlDecoded('Caption') : $logIt->getHtmlDecoded('Caption'),
                'details' => $logIt->getDateFormattedShort('RecordCreated') . ' ' . $logIt->getTimeFormat('RecordCreated') . ', ' .
                    $this->getChangeLogActionName($logIt->get('ChangeKind')),
                'content' => $logIt->getHtmlDecoded('Content'),
                'userpic' => $logIt->get('SystemUser') != '' ? $this->getUserPicUrl($logIt->get('SystemUser')) : '',
                'priority' => $anchorIt->get('PriorityColor'),
                'url' => '/mobile/form/' . get_class($anchorIt->object) . '/' . $anchorIt->getId()
            );
            $logIt->moveNext();
        }
        return $data;
    }

    public function getWorkCards()
    {
        $object = getFactory()->getObject('WorkItem');
        $object->disableVpd();
        $objectRegistry = $this->getRegistry($object);

        $data = array();
        $it = $objectRegistry->Query(
            array(
                new \WorkItemStatePredicate('initial,progress'),
                new \FilterAttributePredicate('Assignee', $this->userIt->getId()),
                new \SortAttributeClause('Priority'),
                new \SortAttributeClause('DueDate'),
                new \SortAttributeClause('OrderNum')
            )
        );

        $item = 0;
        while( !$it->end() && $item++ < $this->maxItems )
        {
            $workItemIt = $it->getObjectIt();
            $projectIt = $it->getRef('Project');

            $data[] = array(
                'id' => $this->lastItem + $item,
                'title' => $it->getHtmlDecoded('CaptionNative'),
                'details' => sprintf(text(2901),
                        $it->getHtmlDecoded('TypeName') != '' ? $it->getHtmlDecoded('TypeName') : $workItemIt->object->getDisplayName(),
                        $it->get('DueDate') != '' ? $it->getDateFormattedShort('DueDate') : $projectIt->getDateFormattedShort('FinishDate'),
                        $projectIt->getDisplayName()
                    ),
                'priority' => $it->get('PriorityColor'),
                'url' => '/mobile/form/' . get_class($workItemIt->object) . '/' . $workItemIt->getId()
            );
            $it->moveNext();
        }
        return $data;
    }

    public function getDiscussionCards()
    {
        $log = getFactory()->getObject('ChangeLogAggregated');
        $logRegistry = $this->getRegistry($log);

        $data = array();
        $logIt = $logRegistry->Query(
            array(
                new \ChangeLogActionFilter('commented'),
                new \ChangeLogVisibilityFilter(),
                new \ChangeLogSinceNotificationFilter($this->userIt),
                new \ChangeLogParticipantFilter('notme'),
                new \FilterVpdPredicate(),
                new \SortChangeLogRecentClause()
            )
        );

        $item = 0;
        while( !$logIt->end() && $item++ < $this->maxItems )
        {
            $author = $logIt->get('UserName');
            if ( $author == '' ) $author = $logIt->get('AuthorName');

            $matches = array();
            if ( preg_match('/O-([\d]+)/i', $logIt->getHtmlDecoded('Content'), $matches) ) {
                $commentId = $matches[1];
            }

            $data[] = array(
                'id' => $this->lastItem + $item,
                'title' => $author,
                'details' => preg_replace('/O-[\d]+/i', '', $logIt->getHtmlDecoded('Content')),
                'userpic' => $logIt->get('SystemUser') != '' ? $this->getUserPicUrl($logIt->get('SystemUser')) : '',
                'when' => $logIt->getDateFormattedShort('RecordCreated') . ' ' . $logIt->getTimeFormat('RecordCreated'),
                'url' => '/mobile/form/' . $logIt->get('ClassName') . '/' . $logIt->get('ObjectId') . '#c' . $commentId
            );
            $logIt->moveNext();
        }
        return $data;
    }

    public function getPeopleCards()
    {
        $data = array();

        $projectIt = getFactory()->getObject('ProjectActive')->getAll();
        $workItem = getFactory()->getObject('WorkItem');
        $workItemRegistry = $workItem->getRegistry();
        $object = getFactory()->getObject('ProjectUser');
        $registry = $object->getRegistry();

        $it = $registry->Query(
            array()
        );
        while( !$it->end() ) {
            $taskIt = $workItemRegistry->Query(
                array(
                    new \FilterVpdPredicate(),
                    new \WorkItemStatePredicate('initial,progress'),
                    new \FilterAttributePredicate('Assignee', $it->getId()),
                    new \SortAttributeClause('DueDate.D'),
                    new \SortAttributeClause('Priority'),
                )
            );
            $projects = array();
            $tasksTotal = $taskIt->count();
            $tasksLeftWork = 0;
            while( !$taskIt->end() ) {
                $objectIt = $taskIt->getObjectIt();
                $projects[$taskIt->get('Project')][] = array(
                    'id' => $taskIt->get('UID'),
                    'title' => $taskIt->getDisplayName(),
                    'url' => '/mobile/form/' . get_class($objectIt->object) . '/' . $objectIt->getId()
                );
                $tasksLeftWork += $taskIt->get('LeftWork');
                $taskIt->moveNext();
            }
            $groups = array();
            foreach( $projects as $projectId => $tasks ) {
                $projectIt->moveToId($projectId);
                $groups[] = array(
                    'icon' => 'stack',
                    'title' => $projectIt->getDisplayName(),
                    'items' => array_slice($tasks, 0, 10)
                );
            }
            $data[] = array (
                'title' => $it->getHtmlDecoded('Caption'),
                'userpic' => $this->getUserPicUrl($it->getId()),
                'details' => sprintf(text(2902), $tasksTotal, $tasksLeftWork),
                'groups' => $groups
            );
            $it->moveNext();
        }

        return $data;
    }

    public function getProjectsCards()
    {
        $data = array();

        $iteration = getFactory()->getObject('Iteration');
        $iterationRegistry = $iteration->getRegistry();
        $iterationRegistry->setLimit(10);

        $release = getFactory()->getObject('Release');
        $releaseRegistry = $release->getRegistry();
        $releaseRegistry->setLimit(10);

        $workItem = getFactory()->getObject('WorkItem');
        $workItem->disableVpd();
        $workItemRegistry = $workItem->getRegistry();

        $milestone = getFactory()->getObject('Milestone');
        $milestoneRegistry = $milestone->getRegistry();
        $milestoneRegistry->setLimit(10);

        $object = getFactory()->getObject('ProjectActive');
        $registry = $object->getRegistry();

        $it = $registry->Query(
            array(
                new \ProjectAccessibleVpdPredicate()
            )
        );
        while( !$it->end() ) {
            $iterationIt = $iterationRegistry->Query(
                array(
                    new \FilterVpdPredicate($it->get('VPD')),
                    new \IterationTimelinePredicate(\IterationTimelinePredicate::NOTPASSED)
                )
            );
            $workItemPredicateKey = 'Release';
            if ( $iterationIt->count() < 1 ) {
                $iterationIt = $releaseRegistry->Query(
                    array(
                        new \FilterVpdPredicate($it->get('VPD')),
                        new \ReleaseTimelinePredicate('not-passed')
                    )
                );
                $workItemPredicateKey = 'PlannedRelease';
            }

            $taskIt = $workItemRegistry->Query(
                array(
                    new \FilterAttributePredicate($workItemPredicateKey, $iterationIt->idsToArray()),
                    new \WorkItemStatePredicate('initial,progress'),
                    new \SortAttributeClause('DueDate.D'),
                    new \SortAttributeClause('Priority'),
                )
            );

            $stages = array();
            while( !$taskIt->end() ) {
                $objectIt = $taskIt->getObjectIt();
                $stages[$taskIt->get($workItemPredicateKey)][] = array(
                    'id' => $taskIt->get('UID'),
                    'title' => $taskIt->getDisplayName(),
                    'url' => '/mobile/form/' . get_class($objectIt->object) . '/' . $objectIt->getId()
                );
                $taskIt->moveNext();
            }

            $groups = array();
            foreach( $stages as $stageId => $tasks ) {
                $iterationIt->moveToId($stageId);
                $offset = $iterationIt->getFinishOffsetDays();
                $title = $iterationIt->getDisplayName() .
                    ' &nbsp; [' .
                        $iterationIt->getDateFormattedShort('StartDate') . ' : ' .
                        $iterationIt->getDateFormattedShort('EstimatedFinishDate').
                    ']';
                if ( $offset > 0 ) {
                    $title .= ' &nbsp; -' . $offset . ' ' . translate('дн.');
                }
                $groups[] = array(
                    'icon' => $offset > 0 ? 'meteo-lightning' : 'stack',
                    'title' => $title,
                    'items' => array_slice($tasks, 0, 10)
                );
            }

            $milestoneIt = $milestoneRegistry->Query(
                array(
                    new \FilterVpdPredicate($it->get('VPD')),
                    new \MilestoneTimelinePredicate('not-passed')
                )
            );
            while( !$milestoneIt->end() ) {
                $tasks = array();
                $requestIt = $milestoneIt->getRef('TraceRequests');
                while( !$requestIt->end() ) {
                    $tasks[] = array(
                        'id' => $requestIt->get('UID'),
                        'title' => $requestIt->getDisplayName(),
                        'closed' => $requestIt->get('FinishDate') != '',
                        'url' => '/mobile/form/' . get_class($requestIt->object) . '/' . $requestIt->getId()
                    );
                    $requestIt->moveNext();
                }
                $groups[] = array(
                    'icon' => 'line-calendar',
                    'title' => $milestoneIt->getHtmlDecoded('Caption')
                                    . ' [' . $milestoneIt->getDateFormattedShort('MilestoneDate'). ']',
                    'items' => array_slice($tasks, 0, 10)
                );
                $milestoneIt->moveNext();
            }

            $data[] = array (
                'title' => $it->getHtmlDecoded('Caption'),
                'statecolor' => 'green',
                'details' => text(2903),
                'groups' => $groups
            );
            $it->moveNext();
        }

        return $data;
    }

    public function getWikiCards($className, $path)
    {
        if ( !class_exists(getFactory()->getClass($className)) ) return array();

        $object = getFactory()->getObject($className);
        $objectRegistry = $this->getRegistry($object);

        $data = array();
        $it = $objectRegistry->Query(
            array(
                new \FilterVpdPredicate(),
                new \WikiRootFilter()
            )
        );

        $item = 0;
        while( !$it->end() && $item++ < $this->maxItems )
        {
            $title = \TextUtils::stripAnyTags($it->getHtmlDecoded('Caption'));
            if ( $it->get('DocumentVersion') != '' ) {
                $title .= ' ['.$it->getHtmlDecoded('DocumentVersion').']';
            }

            if ( $title == translate('База знаний') ) {
                $title = $it->getRef('Project')->getDisplayName();
            }

            $data[] = array(
                'id' => $this->lastItem + $item,
                'title' => $title,
                'priority' => $it->getStateIt()->get('RelatedColor'),
                'entity' => get_class($object) . ':' . $it->getId(),
                'url' => $path . '/' . get_class($object) . '/' . $it->getId()
            );
            $it->moveNext();
        }
        return $data;
    }

    public function getWikiHierarchy( $className, $objectId )
    {
        if ( !class_exists(getFactory()->getClass($className)) ) return array();

        $object = getFactory()->getObject($className);
        $objectIt = $object->getExact($objectId);
        if ( $objectIt->getId() == '' ) return array();

        $registry = new \WikiPageRegistryContent($object);
        $pageIt = $registry->Query(
            array(
                new \ParentTransitiveFilter($objectIt->getId()),
                new \SortAttributeClause('ParentPage')
            )
        );

        $editor = \WikiEditorBuilder::build();
        $parser = $editor->getHtmlParser();
        return array(
            'header' => $objectIt->getDisplayName(),
            'pages' => $this->buildHierarchyPages($pageIt, $objectIt->getId(), $parser),
            'content' => $parser->parse($objectIt->getHtmlDecoded('Content'))
        );
    }

    protected function buildHierarchyPages( $objectIt, $parentId, $parser )
    {
        $pages = array();

        $copyIt = $objectIt->copyAll();
        $copyIt->moveTo('ParentPage', $parentId);

        while( !$copyIt->end() && $copyIt->get('ParentPage') == $parentId ) {
            $parser->setObjectIt($copyIt->copy());
            $pages[] = array(
                'id' => $copyIt->getId(),
                'title' => $copyIt->get('Caption'),
                'content' => $parser->parse($copyIt->getHtmlDecoded('Content')),
                'pages' => $this->buildHierarchyPages($objectIt, $copyIt->getId(), $parser)
            );
            $copyIt->moveNext();
        }

        return $pages;
    }

    public function getBuildCards()
    {
        $data = array();
        $object = getFactory()->getObject('Build');
        $objectIt = $this->getRegistry($object)->Query(
            array(
                new \FilterVpdPredicate(),
                new \SortRecentClause()
            )
        );

        while( !$objectIt->end() ) {
            $stateIt = $objectIt->getRef('State');

            $tests = array();
            if ( $objectIt->get('Tests') != '' ) {
                $testIt = $objectIt->getRef('Tests');
                while( !$testIt->end() ) {
                    $key = $testIt->get('Caption').$testIt->get('TestScenario');
                    $tests[$key] = array(
                        'resultcolor' => $testIt->get('ResultColor') != '' ? $testIt->get('ResultColor') : 'YellowGreen'
                    );
                    $testIt->moveNext();
                }
            }

            $data[] = array(
                'id' => $objectIt->getId(),
                'title' => $objectIt->getDisplayName(),
                'statecolor' => $stateIt->get('ColorCode'),
                'state' => $stateIt->getDisplayName(),
                'tests' => array_values($tests),
                'url' => '/mobile/form/' . get_class($objectIt->object) . '/' . $objectIt->getId()
            );

            $objectIt->moveNext();
        }

        return $data;
    }

    public function getTestCards()
    {
        $data = array();
        $object = getFactory()->getObject('TestExecution');
        $objectIt = $this->getRegistry($object)->Query(
            array(
                new \FilterVpdPredicate(),
                new \SortRecentClause()
            )
        );

        while( !$objectIt->end() )
        {
            $case = getFactory()->getObject('pm_TestCaseExecution');
            $case->addFilter( new \FilterAttributePredicate('Test', $objectIt->getId()));
            $stats_it = $case->getStats();

            $tests = array();
            if ( $stats_it->get('Passed') > 0 ) {
                $tests[] = array (
                    'resultcolor' => 'YellowGreen',
                    'count' => $stats_it->get('Passed')
                );
            }
            if ( $stats_it->get('NotRun') > 0 ) {
                $tests[] = array (
                    'resultcolor' => 'orange',
                    'count' => $stats_it->get('NotRun')
                );
            }
            if ( $stats_it->get('Failed') > 0 ) {
                $tests[] = array (
                    'resultcolor' => 'red',
                    'count' => $stats_it->get('Failed')
                );
            }

            $details = array_filter(
                array(
                    $objectIt->getDateFormattedShort('RecordCreated') . ' ' . $objectIt->getTimeFormat('RecordCreated'),
                    $objectIt->getRef('Version')->getDisplayName(),
                    $objectIt->getRef('Environment')->getDisplayName(),
                    getSession()->getLanguage()->getHoursWording($objectIt->get('Duration')),
                ),
                function($item) {
                    return $item != '';
                }
            );

            $data[] = array(
                'id' => $objectIt->getId(),
                'title' => $objectIt->get('Caption'),
                'statecolor' => $objectIt->get('ResultColor') != '' ? $objectIt->get('ResultColor') : 'YellowGreen',
                'details' => join(', ', $details),
                'tests' => array_values($tests),
                'url' => '/mobile/form/' . get_class($objectIt->object) . '/' . $objectIt->getId()
            );

            $objectIt->moveNext();
        }

        return $data;
    }

    protected function getRegistry( $object )
    {
        $registry = $object->getRegistry();
        $registry->setLimit($this->lastItem + $this->maxItems);
        $registry->setOffset($this->lastItem);
        return $registry;
    }

    protected function getChangeLogActionName( $kind )
    {
        switch( $kind ) {
            case 'submitted':
            case 'deleted':
            case 'modified':
            case 'commented':
                return $kind;
            default:
                return $kind;
        }
    }

    public function getUserPicUrl( $id )
    {
        $size = 30;

        $sprites_on_row = floor(32767 / $size);
        $row = floor($id / $sprites_on_row);
        $column = $id - $row * $sprites_on_row - 1;
        $timestamp = getSession()->getUserPicTimestamp();

        return "background: url('/images/userpics-middle.png?v=". $timestamp ."') no-repeat -".
            ($column * $size) ."px ". (max(1, -1 * $row * $size)) ."px;";
    }

    public function buildObject( $className )
    {
        $object = getFactory()->getObject($className);

        $builder = new \WorkflowModelBuilder();
        $builder->build($object);

        if ( $object instanceof \Task ) {
            $object->setAttributeType('Assignee', 'REF_ProjectUserId');
            $object->setAttributeType('Release', 'REF_IterationActualId');
        }
        if ( $object instanceof \Request ) {
            $object->setAttributeType('Owner', 'REF_ProjectUserId');
            $object->setAttributeType('Iteration', 'REF_IterationActualId');
            $object->setAttributeType('PlannedRelease', 'REF_ReleaseActualId');
        }
        if ( $object instanceof \Question ) {
            $object->setAttributeType('Owner', 'REF_ProjectUserId');
        }
        $object->setAttributeVisible('Project', true);

        return $object;
    }
}