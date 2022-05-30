<?php

namespace plugins\support\classes;

use CustomizableObjectSet;
use DevpromTestCase;
use PhpImap\IncomingMail;
use MailboxMessage;
use PHPUnit_Framework_MockObject_MockObject;
use Devprom\ProjectBundle\Service\Model\ModelService;

include_once SERVER_ROOT_PATH . "/plugins/support/classes/MailboxScanner.php";
include_once SERVER_ROOT_PATH . "/plugins/support/classes/MailboxMessage.php";
include_once SERVER_ROOT_PATH . "/core/classes/project/Project.php";
include_once SERVER_ROOT_PATH . "/core/classes/project/ProjectImportance.php";
include_once SERVER_ROOT_PATH . "/pm/classes/comments/Comment.php";
include_once SERVER_ROOT_PATH . "/pm/classes/issues/Request.php";
include_once SERVER_ROOT_PATH . "/core/classes/user/User.php";

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class MailboxScannerTest extends DevpromTestCase {

    private $commentMock, $attachmentMock, $watcherMock, $requestMock,
        $userItMock, $requestItMock, $userServiceMock, $objectUID, $projectIt;

    private $session;

    /** @var  MockScannerBuilder */
    private $mockScannerBuilder;

    public function setUp()
    {
        parent::setUp();

        $this->commentMock = $this->getMockBuilder(\Comment::class)
            ->setConstructorArgs(array())
            ->setMethods(['add_parms'])
            ->getMock();

        $this->attachmentMock = $this->getMockBuilder(\Attachment::class)
            ->setConstructorArgs(array())
            ->getMock();

        $this->userServiceMock = $this->getMockBuilder(UserService::class)
            ->setConstructorArgs(array())
            ->setMethods(["authorizeExistingUser", "isServicedeskProject"])
            ->getMock();

        $this->userServiceMock->expects($this->any())->method("isServicedeskProject")->will($this->returnValue(false));
        $this->userServiceMock->expects($this->any())->method('authorizeExistingUser')->will(
            $this->returnValue(
                (new \User())->getEmptyIterator()
            ));

        $this->requestMock = $this->getMockBuilder(\Request::class)
            ->setConstructorArgs(array())
            ->setMethods(["add_parms", "getRegistry", "getExact", "getByRefArray", "createSQLIterator", "getTerminalStates"])
            ->getMock();

        $this->requestRegistryMock = $this->getMockBuilder(\ObjectRegistrySQL::class)
            ->setConstructorArgs(array($this->requestMock))
            ->setMethods(["Create"])
            ->getMock();
        $this->requestRegistryMock->expects($this->any())->method('Create')->will(
            $this->returnValue(
                $this->requestMock->createCachedIterator(array(
                    array(
                        'Caption' => "Existing request",
                        'pm_ChangeRequestId' => '1'
                    )))
            )
        );
        $this->requestMock->expects($this->any())->method('getRegistry')->will(
            $this->returnValue($this->requestRegistryMock)
        );
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

        $this->watcherMock = $this->getMockBuilder(\Watcher::class)
            ->setConstructorArgs(array($this->requestMock->createSQLIterator('')))
            ->setMethods(["add_parms", "createSQLIterator"])
            ->getMock();

        $this->project = $this->watcherMock;
        $this->user = $this->watcherMock;
        
        getFactory()->expects($this->any())->method('createInstance')->will( $this->returnValueMap(
            array (
                array ( 'pm_Watcher', $this->requestMock->createSQLIterator(''), $this->watcherMock ),
                array ( 'Watcher', $this->requestMock->createSQLIterator(''), $this->watcherMock ),
                array ( 'ProjectImportance', null, new \ProjectImportance() ),
                array ( 'pm_Project', null, $this->project ),
                array ( 'Project', null, $this->project ),
                array ( 'cms_User', null, $this->user ),
                array ( 'User', null, $this->user ),
                array ( 'Severity', null, $this->user ),
                array ( 'Priority', null, $this->user),
                array ( 'PMCustomAttribute', null, new \PMCustomAttribute())
            )
        ));
                
        $this->project = $this->getMockBuilder(\Project::class)
            ->setConstructorArgs(array())
            ->setMethods(["createIterator"])
            ->getMock();

        $this->projectIt = $this->getMockBuilder(\ProjectIterator::class)
            ->setConstructorArgs(array($this->project))
            ->setMethods(['getProjectIt','getMailboxIterator'])
            ->getMock();

        $this->projectIt->expects($this->any())->method('getProjectIt')->will($this->returnValue($this->projectIt));
        $this->projectIt->expects($this->any())->method('getMailboxIterator')->will($this->returnValue($this->projectIt));

        $this->session = $this->getSessionObject();

        $this->objectUID = $this->getMockBuilder(\ObjectUID::class)
            ->setConstructorArgs(array(''))
            ->setMethods(["getUIDInfo","getProject","getObjectIt"])
            ->getMock();

        $this->objectUID->expects($this->any())->method("getUIDInfo")->will($this->returnValue(array()));
        $this->objectUID->expects($this->any())->method("getProject")->will($this->returnValue($this->projectIt));

        $this->modelService = new ModelService(
            new \ModelValidator(
                array (
                    new \ModelValidatorObligatory(),
                    new \ModelValidatorTypes()
                )
            ),
            new \ModelDataTypeMapper(),
            array(),
            $this->objectUID
        );

        $this->mockScannerBuilder = new MockScannerBuilder(
            $this->requestMock, $this->commentMock, $this->attachmentMock,  $this->userServiceMock, $this->objectUID,
            $this->userItMock, $this->requestItMock, $this->modelService);
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
        return new MailboxScanner($this->requestMock, $this->commentMock, $this->attachmentMock, $this->attachmentMock,
            $this->userServiceMock, $this->objectUID, $this->modelService);
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

        $mail_message = new MailboxMessage($msg, null);
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
                                &$userServiceMock, &$objectUID, &$userItMock, &$requestItMock,&$modelService) {
        $this->requestMock = $requestMock;
        $this->commentMock = $commentMock;
        $this->attachmentMock = $attachmentMock;
        $this->userServiceMock = $userServiceMock;
        $this->objectUID = $objectUID;
        $this->userItMock = $userItMock;
        $this->requestItMock = $requestItMock;
        $this->modelService = $modelService;
    }

    /**
     * @return MockScannerBuilder
     */
    public function newInstance() {
        $this->scanner = new MailboxScanner($this->requestMock, $this->commentMock, $this->attachmentMock, $this->attachmentMock,
            $this->userServiceMock, $this->objectUID, $this->modelService);
        return $this;
    }

    /**
     * @return MockScannerBuilder
     */
    public function forInternalUser() {
        $this->userServiceMock->expects(\PHPUnit\Framework\TestCase::any())
            ->method("authorizeExistingUser")->will(\PHPUnit\Framework\TestCase::returnValue($this->userItMock));
        return $this;
    }

    /**
     * @return MockScannerBuilder
     */
    public function receivedMailWithIssueIdInSubject() {
        $this->objectUID->expects(\PHPUnit\Framework\TestCase::any())->method("getObjectIt")
            ->with(\PHPUnit\Framework\TestCase::equalTo("I-1"))->will(\PHPUnit\Framework\TestCase::returnValue($this->requestItMock));
        return $this;
    }

    /**
     * @return MailboxScanner
     */
    public function get() {
        return $this->scanner;
    }

}
