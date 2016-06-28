<?php

namespace Devprom\CommonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SpriteController extends Controller
{
    public function showAction(Request $request)
    {
		$fileName = $request->get('filename');

		if ( $fileName == '' ) throw new NotFoundHttpException();
		if ( preg_match('/[a-zA-Z0-9_\-\.]+/i', $fileName) === false ) throw new NotFoundHttpException();

		$filePath = SERVER_ROOT_PATH . "images/".$fileName;
		if ( !file_exists($filePath) ) throw new NotFoundHttpException();

		$fileDateTime = gmdate( "D, d M Y H:i:s T", filemtime($filePath) );
		$response = new Response();
		$response->setETag(md5($fileDateTime));

		if ( $response->isNotModified($request) ) return $response;

		$response->headers->set('Content-Type', 'image/png');
		$response->headers->set('Content-Disposition', 'attachment; filename='.$fileName);
		$response->headers->set('Content-Transfer-Encoding', 'binary');

		$response->setPublic();
		$response->setLastModified(new \DateTime($fileDateTime));
		$response->setContent(file_get_contents($filePath));

    	return $response;
    }
}