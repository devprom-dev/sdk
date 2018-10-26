<?php

namespace Devprom\ProjectBundle\Controller;

use Devprom\CommonBundle\Controller\PageController;
use Devprom\ProjectBundle\Service\Files\UploadFileService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

include_once SERVER_ROOT_PATH."pm/views/ui/Common.php";
include_once SERVER_ROOT_PATH."pm/views/attachments/AttachmentsPage.php";

class AttachmentsController extends PageController
{
    public function pageAction( Request $request )
    {
        $_REQUEST['report'] = $request->get('report');
    	return $this->responsePage( new \AttachmentsPage() );
    }

    public function uploadAction( Request $request )
    {
        $service = new UploadFileService();
        return new JsonResponse(
            $service->process(
                $request->get('objectId'),
                $request->get('objectClass'),
                $request->get('attachmentClass')
            )
        );
    }
}