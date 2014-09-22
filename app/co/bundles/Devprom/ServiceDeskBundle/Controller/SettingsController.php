<?php

namespace Devprom\ServiceDeskBundle\Controller;

use Devprom\ServiceDeskBundle\Entity\Project;
use Devprom\ServiceDeskBundle\Service\SettingsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Yaml\Yaml;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class SettingsController extends Controller {

    /**
     * @Route("/settings", name="settings_dashboard")
     * @Method("GET")
     * @Template()
     */
    public function dashboardAction() {
        if ($this->container->getParameter('supportProjectId')) {
            return $this->redirect($this->generateUrl('issue_list', array(), true));
        }

        $settings = $this->getSettingsService()->load();
        if (!$settings['appUrl']) {
            $settings['appUrl'] = str_replace($this->getRequest()->getPathInfo(), "", $this->getRequest()->getUri());
        }
        $form = $this->createSettingsForm($settings);

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/settings", name="settings_save")
     * @Method("POST")
     * @Template()
     */
    public function saveAction(Request $request) {

        $form = $this->createSettingsForm(array());

        $form->bind($request);

        if ($form->isValid()) {
            $savedSettings = $this->getSettingsService()->load();
            $newSettings = $form->getData();
            $settings = array_merge($savedSettings, $newSettings);

            $url = $this->generateUrl('issue_list');
            
            $this->getSettingsService()->save($settings);

            return $this->redirect($url);
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @return array
     */
    protected function getProjectsList()
    {
        $projects = $this->getDoctrine()->getManager()->getRepository("DevpromServiceDeskBundle:Project")->findAll();
        $projectList = array();
        /** @var Project $project */
        foreach ($projects as $project) {
            $projectList[$project->getId()] = mb_convert_encoding($project->getName(), 'windows-1251', 'windows-1251');
        }
        return $projectList;
    }

    /**
     * @return SettingsService
     */
    protected function getSettingsService() {
        return $this->container->get('settings_service');
    }

    /**
     * @param $settings
     * @return \Symfony\Component\Form\Form
     */
    protected function createSettingsForm($settings)
    {
        return $this->createFormBuilder($settings)
            ->add("supportProjectId", "choice", array(
                'choice_list' => new SimpleChoiceList($this->getProjectsList()),
                'empty_value' => '',
                'label' => 'settings.supportProjectId.title',
                'translation_domain' => 'settings'
            ))
            ->add("appUrl", "url", array(
                'label' => 'settings.appUrl.title',
                'required' => false,
                'translation_domain' => 'settings'
            ))
            ->getForm();
    }


}
