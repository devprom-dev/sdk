<?php
use Devprom\ServiceDeskBundle\Service\UserService;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class UserServiceTest extends \PHPUnit\Framework\TestCase {

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $mailer;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $userManager;

    /** @var  UserService */
    private $service;

    /** @var  changeLogService */
    private $changeLogService;

    protected function setUp()
    {
        $this->mailer = $this->getMockBuilder("Devprom\ServiceDeskBundle\Mailer\Mailer")
            ->disableOriginalConstructor()
            ->getMock();
        $this->userManager = $this->getMockBuilder("FOS\UserBundle\Doctrine\UserManager")
            ->disableOriginalConstructor()
            ->getMock();
        $this->changeLogService = $this->getMockBuilder("Devprom\ServiceDeskBundle\Service\ObjectChangeLogger")
            ->disableOriginalConstructor()
            ->getMock();
        $this->userManager->expects($this->any())
            ->method("createUser")
            ->will($this->returnCallback(function() {
                return new Devprom\ServiceDeskBundle\Entity\User();
            }));
        $this->userManager->expects($this->any())
            ->method("updatePassword")
            ->will($this->returnCallback(function($user) {
                $user->setPassword(md5($user->getPassword()));
                return $user;
            }));
        $this->entityManager = $this->getMockBuilder("Doctrine\ORM\EntityManager")
            ->disableOriginalConstructor()
            ->getMock();
        $this->service = new \Devprom\ServiceDeskBundle\Service\UserService($this->entityManager, $this->userManager, $this->mailer, $this->changeLogService);
    }


    /**
     * @test
     */
    public function shouldGenerateDifferentPasswords() {

        $firstUser = $this->service->registerUser("first@domain");
        $secondUser = $this->service->registerUser("second@domain");

        $this->assertNotEquals($firstUser->getPlainPassword(), $secondUser->getPlainPassword());
    }

    /**
     * @test
     */
    public function shouldPersistUserWithEncodedPassword() {
        $this->userManager->expects($this->once())->method("updateCanonicalFields");
        $this->userManager->expects($this->once())->method("updatePassword");
        $this->userManager->expects($this->once())->method("updateUser");

        $user = $this->service->registerUser("user@domain");

        $this->assertNotSame($user->getPlainPassword(), $user->getPassword());
    }

    /**
     * @test
     */
    public function shouldSendRegistrationEmail() {
        $this->mailer->expects($this->once())->method("sendRegistrationEmailMessage");
        $this->service->registerUser("user@domain");
    }

    /**
     * @test
     */
    public function shouldUseEmailAsNameIfNameIsNotSpecified() {
        $user = $this->service->registerUser("user@domain");
        $this->assertEquals($user->getEmail(), $user->getUsername());
    }

    /**
     * @test
     */
    public function shouldCreateProperUserRecordWithName() {
        $userName = "User";
        $userEmail = "user@domain";

        $user = $this->service->registerUser($userEmail, $userName);

        $this->assertEquals($userEmail, $user->getEmail());
        $this->assertEquals($userName, $user->getUsername());
        $this->assertTrue($user->isEnabled());
        $this->assertNotEmpty($user->getPassword());
    }


}