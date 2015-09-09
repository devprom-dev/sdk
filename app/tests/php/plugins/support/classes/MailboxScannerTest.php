<?php

namespace plugins\support\classes;

use DevpromTestCase;
use IncomingMail;
use MailboxMessage;
use PHPUnit_Framework_MockObject_MockObject;
use Project;
use Request;

include_once SERVER_ROOT_PATH . "/plugins/support/classes/MailboxScanner.php";
include_once SERVER_ROOT_PATH . "/plugins/support/classes/MailboxMessage.php";
include_once SERVER_ROOT_PATH . "/ext/imap/ImapMailbox.php";
include_once SERVER_ROOT_PATH . "/core/classes/project/Project.php";
include_once SERVER_ROOT_PATH . "/core/classes/project/ProjectImportance.php";
include_once SERVER_ROOT_PATH . "/pm/classes/comments/Comment.php";
include_once SERVER_ROOT_PATH . "/pm/classes/issues/Request.php";
include_once SERVER_ROOT_PATH . "/core/classes/user/User.php";

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class MailboxScannerTest extends DevpromTestCase {

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $commentMock, $attachmentMock, $watcherMock, $watcherIterator, $requestMock,
        $userItMock, $requestItMock, $userServiceMock, $objectUID, $projectIt;

    private $session;

    /** @var  MockScannerBuilder */
    private $mockScannerBuilder;

    public function setUp() {
        parent::setUp();
        //$this->markTestSkipped("Невозможно поддерживать");
        $this->commentMock = $this->getMock("Comment", array("add_parms"));
        $this->attachmentMock = $this->getMock("Attachment");
        $this->objectUID = $this->getMock("ObjectUID");

        $this->userServiceMock = $this->getMock("UserService", array("authorizeExistingUser", "isServicedeskProject"));
        $this->userServiceMock->expects($this->any())->method("isServicedeskProject")->will($this->returnValue(false));

        $this->requestMock = $this->getMock("Request", array("add_parms", "getExact", "getByRefArray", "createSQLIterator", "getTerminalStates"));
        $this->requestMock->expects($this->any())->method('add_parms')->will($this->returnValue(1));
        $this->requestMock->expects($this->any())->method('getExact')->will(
        		$this->returnValue(
						$this->requestMock->createCachedIterator(array(
	   						array( 
	   								'Caption' => "Existing request",
	  								'pm_ChangeRequestId' => '1'
	   						)))        		
        		));
        $this->requestMock->expects($this->any())->method('createSQLIterator')->will(
        		$this->returnValue(
        				$this->requestMock->createCachedIterator(array(
        						array( 
        								'Caption' => "Existing request",
        								'pm_ChangeRequestId' => '1'
        						)))
        		));

        $this->requestItMock = $this->requestMock->createSQLIterator('');
        $this->watcherMock = $this->getMock("Watcher", array("add_parms", "createSQLIterator"), array($this->requestMock->createSQLIterator('')));
        $this->project = $this->watcherMock;
        
        getFactory()->expects($this->any())->method('createInstance')->will( $this->returnValueMap(
            array (
                array ( 'pm_Watcher', $this->requestMock->createSQLIterator(''), $this->watcherMock ),
                array ( 'Watcher', $this->requestMock->createSQLIterator(''), $this->watcherMock ),
                array ( 'ProjectImportance', null, new \ProjectImportance() ),
                array ( 'pm_Project', null, $this->project ),
                array ( 'Project', null, $this->project )
            )
        ));
                
        $this->project = $this->getMock('Project', array('createIterator'));
        $this->projectIt = $this->getMock("ProjectIterator", array(), array($this->project));
        $this->projectIt->expects($this->any())->method('getId')->will($this->returnValue(5));
        $this->session = $this->getSessionObject();

        $this->mockScannerBuilder = new MockScannerBuilder(
            $this->requestMock, $this->commentMock, $this->attachmentMock,  $this->userServiceMock, $this->objectUID,
            $this->userItMock, $this->requestItMock);

    }

    /**
     * @test
     */
    public function shouldCreateNewRequest() {
        $subject = "Subject!";
        $body = "Hey there!";

        $this->watcherMock->expects($this->any())->method('createSQLIterator')->will(
        		$this->returnValue(
        				$this->watcherMock->createCachedIterator(array())
        		));
        
        // set up expectations
        $this->requestMock->expects($this->once())->method('add_parms')->with($this->contains($body));
        $this->requestMock->expects($this->once())->method('add_parms')->with($this->contains($subject));

        // exercise
        $mail_message = $this->buildMessage($subject, $body);
        $scanner = $this->buildScanner();
        $scanner->processMessage($mail_message, $this->session, $this->projectIt);
    }

    /**
     * @test
     */
    public function shouldCreateNewCommentIfRequestIdSpecifiedInSubject() {
        $scanner = $this->mockScannerBuilder->newInstance()->receivedMailWithIssueIdInSubject()->get();

        // set up expectations
        $this->requestMock->expects($this->never())->method('add_parms');
        $this->commentMock->expects($this->once())->method('add_parms');

        // exercise
        $mail_message = $this->buildMessageForWithIssueIdInSubject();
        $scanner->processMessage($mail_message, $this->session, $this->projectIt);
    }

    /**
     * @test
     */
    public function shouldCreateNewCommentIfRequestEqualToSubjectExists() {
        $subject = "Existing request";

        $this->requestMock->expects($this->any())->method('getByRefArray')->will(
        		$this->returnValue(
        				$this->requestMock->createSQLIterator('')
        		));
        
        $this->watcherMock->expects($this->any())->method('createSQLIterator')->will(
        		$this->returnValue(
        				$this->watcherMock->createCachedIterator(array(array('pm_WatcherId' => 1)))
        		));
        
        // set up expectations
        $this->requestMock->expects($this->never())->method('add_parms');
        $this->commentMock->expects($this->once())->method('add_parms');

        // exercise
        $scanner = $this->buildScanner();
        $mail_message = $this->buildMessage($subject);
        $scanner->processMessage($mail_message, $this->session, $this->projectIt);
    }

    /**
     * @test
     */
    public function shouldNotAddNewWatcherWhenCommentReceived() {

        $scanner = $this->mockScannerBuilder->newInstance()->receivedMailWithIssueIdInSubject()->get();

        $this->watcherMock->expects($this->any())->method('createSQLIterator')->will(
        		$this->returnValue(
        				$this->watcherMock->createCachedIterator(array())
        		));
        // set up expectations
        $this->watcherMock->expects($this->never())->method("add_parms");

        // exercise
        $mail_message = $this->buildMessageForWithIssueIdInSubject();
        $scanner->processMessage($mail_message, $this->session, $this->projectIt);
    }

    protected function buildScanner() {
        return new MailboxScanner($this->requestMock, $this->commentMock, $this->attachmentMock,
            $this->userServiceMock, $this->objectUID);
    }


    /**
     * @return MailboxMessage
     */
    protected function buildMessage($subject = "qweqw11", $body = "ewwwwdd")
    {
        $msg = new IncomingMail();
        $msg->subject = $subject;
        $msg->textHtml = $body;
        $msg->fromName = "test";
        $msg->fromAddress = "test@test";

        $mail_message = new MailboxMessage($msg, $project_it);
        return $mail_message;
    }

    /**
     * @return MailboxMessage
     */
    protected function buildMessageForWithIssueIdInSubject() {
        return $this->buildMessage("[I-1] Existing request");
    }

}

class MockScannerBuilder {

    /** @var  MailboxScanner */
    private $scanner;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $requestMock, $commentMock, $attachmentMock, $userServiceMock, $objectUID, $userItMock, $requestItMock;

    public function __construct(&$requestMock, &$commentMock, &$attachmentMock,
                                &$userServiceMock, &$objectUID, &$userItMock, &$requestItMock) {
        $this->requestMock = $requestMock;
        $this->commentMock = $commentMock;
        $this->attachmentMock = $attachmentMock;
        $this->userServiceMock = $userServiceMock;
        $this->objectUID = $objectUID;
        $this->userItMock = $userItMock;
        $this->requestItMock = $requestItMock;
    }

    /**
     * @return MockScannerBuilder
     */
    public function newInstance() {
        $this->scanner = new MailboxScanner($this->requestMock, $this->commentMock, $this->attachmentMock,
            $this->userServiceMock, $this->objectUID);
        return $this;
    }

    /**
     * @return MockScannerBuilder
     */
    public function forInternalUser() {
        $this->userServiceMock->expects(\PHPUnit_Framework_TestCase::any())
            ->method("authorizeExistingUser")->will(\PHPUnit_Framework_TestCase::returnValue($this->userItMock));
        return $this;
    }

    /**
     * @return MockScannerBuilder
     */
    public function receivedMailWithIssueIdInSubject() {
        $this->objectUID->expects(\PHPUnit_Framework_TestCase::any())->method("getObjectIt")
            ->with(\PHPUnit_Framework_TestCase::equalTo("I-1"))->will(\PHPUnit_Framework_TestCase::returnValue($this->requestItMock));
        $this->objectUID->expects(\PHPUnit_Framework_TestCase::any())->method("isValidUID")
            ->with(\PHPUnit_Framework_TestCase::equalTo("I-1"))->will(\PHPUnit_Framework_TestCase::returnValue(true));
        return $this;
    }

    /**
     * @return MailboxScanner
     */
    public function get() {
        return $this->scanner;
    }

}
