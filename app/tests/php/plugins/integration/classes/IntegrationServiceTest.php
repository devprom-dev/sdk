<?php

namespace plugins\integration\classes;

use DevpromTestCase;
use PhpImap\IncomingMail;
use MailboxMessage;
use PHPUnit_Framework_MockObject_MockObject;
use Devprom\ProjectBundle\Service\Model\ModelService;

include_once SERVER_ROOT_PATH . "/plugins/integration/classes/IntegrationService.php";
include_once SERVER_ROOT_PATH . "/plugins/integration/model/Integration.php";

class IntegrationServiceTest extends DevpromTestCase
{
    private $service = null;

    public function setUp()
    {
        parent::setUp();

        $this->service = new \IntegrationService(
            (new \Integration())->createCachedIterator(
                array(
                    array()
                )
            ),
            null
        );
    }

    /**
     * @test
     */
    public function extractLinkId()
    {
        $this->assertSame(
            $this->service->extractId('/issues/{id}', 'https://repo-api.mos.ru//issues/31665'),
            '31665'
        );
    }
}
