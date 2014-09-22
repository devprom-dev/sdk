<?php

namespace Devprom\ServiceDeskBundle\Controller;
use Devprom\ServiceDeskBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 *
 */
class LanguageController extends Controller {

    /**
     * @Route("/language/{lang}", name="set_lang")
     * @Method("GET")
     */
    public function setLocaleAction($lang) {
        $this->getRequest()->getSession()->set('_locale', $lang);
        if ($this->getUser()) {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository("DevpromServiceDeskBundle:User")->find($this->getUser()->getId());
            $user->setLanguage($lang);
            $em->persist($user);
            $em->flush($user);
        }

        $referer = $this->getRequest()->headers->get('referer');
        $url = !$referer ? $this->generateUrl("issue_list") : $referer;

        $response = new RedirectResponse($url);
        return $response;
    }

}