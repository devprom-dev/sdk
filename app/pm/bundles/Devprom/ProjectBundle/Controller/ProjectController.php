<?php

namespace Devprom\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Devprom\ProjectBundle\Service\Project\ExportService;

class ProjectController extends Controller
{
    public function exportAction()
    {
    	$project_it = getSession()->getProjectIt();

    	$service = new ExportService();

    	$service->execute($project_it);
    	
        return new RedirectResponse('/pm/'.$project_it->get('CodeName'));
    }
}