<?php

namespace pm\bundles\Devprom\ProjectBundle\Tests\Service;
use Devprom\ProjectBundle\Service\Task\TaskConvertToIssueService;

class TaskConvertToIssueServiceTest extends \DevpromDummyTestCase
{
    function setUp() {
        parent::setUp();
        $this->buildFixtures();
        getFactory()->expects($this->any())->method('createInstance')->will( $this->returnValueMap(
            array (
                array ( 'Request', null, new \Request($this->requestRegistry) ),
                array ( 'Comment', null, new \Comment($this->commentRegistry) ),
                array ( 'ActivityRequest', null, new \ActivityRequest(new \ObjectRegistryMemory) ),
                array ( 'ActivityTask', null, new \ActivityTask(new \ObjectRegistryMemory) ),
                array ( 'Watcher', null, new \Watcher(null, new \ObjectRegistryMemory) ),
                array ( 'TaskTraceBase', null, new \TaskTraceBase(new \ObjectRegistryMemory) ),
                array ( 'RequestTraceBase', null, new \RequestTraceBase(new \ObjectRegistryMemory) ),
                array ( 'Attachment', null, new \Attachment(new \ObjectRegistryMemory) )
            )
        ));
    }

    function buildFixtures() {
        $this->commentRegistry = new \ObjectRegistryMemory(new \Comment);
        $this->commentRegistry->Create(
            array (
                'CommentId' => '1',
                'ObjectClass' => 'Task',
                'ObjectId' => '1'
            )
        );
        $this->taskReqistry = new \ObjectRegistryMemory(new \Task);
        $this->taskReqistry->Create(
            array (
                'pm_TaskId' => '1',
                'Assignee' => '1',
                'Caption' => 'test',
                'State' => 'planned'
            )
        );
        $this->requestRegistry = new \ObjectRegistryMemory(new \Request);
        $this->requestRegistry->Create(
            array (
                'pm_ChangeRequestId' => '5',
                'Caption' => 'test'
            )
        );
    }

    function testRemapTaskAttributes() {
        $service = new TaskConvertToIssueService(getFactory());
        $requestAttributes = $service->mapToIssue(
            array (
                'Assignee' => '1',
                'Caption' => 'test',
                'Release' => '2',
                'State' => 'planned',
                'Planned' => '3'
            )
        );
        $this->assertEquals('1', $requestAttributes['Owner']);
        $this->assertEquals('test', $requestAttributes['Caption']);
        $this->assertEquals('', $requestAttributes['State']);
        $this->assertEquals('2', $requestAttributes['Iteration']);
        $this->assertEquals('3', $requestAttributes['Estimation']);
    }

    function testConvertTaskToIssue() {
        $service = new TaskConvertToIssueService(getFactory());
        $requestIt = $service->mapTaskToIssue($this->taskReqistry->Query(array()));
        $this->assertEquals('test', $requestIt->get('Caption'));
        $this->assertEquals('1', $requestIt->get('Owner'));
    }

    function testCommentsRebinded() {
        $service = new TaskConvertToIssueService(getFactory());
        $service->bindComments(
            $this->taskReqistry->Query(array()),
            $this->requestRegistry->Query(array())
        );
        $comment_it = getFactory()->getObject('Comment')->getRegistry()->Query(array());
        $this->assertEquals('Request', $comment_it->get('ObjectClass'));
        $this->assertEquals('5', $comment_it->get('ObjectId'));
    }

    function testConvertTaskIterator() {
        $service = new TaskConvertToIssueService(getFactory());
        $service->convert($this->taskReqistry->Query(array()));
        $comment_it = getFactory()->getObject('Comment')->getRegistry()->Query(array());
        $this->assertEquals('Request', $comment_it->get('ObjectClass'));
        $this->assertEquals('2', $comment_it->get('ObjectId'));
    }
}
