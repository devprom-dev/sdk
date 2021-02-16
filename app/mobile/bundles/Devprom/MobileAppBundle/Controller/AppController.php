<?php
namespace Devprom\MobileAppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Devprom\MobileAppBundle\Service\App\MobileDataService;
use Devprom\MobileAppBundle\Service\App\MobilePersistService;
use Devprom\MobileAppBundle\Service\App\MobileViewModelService;

class AppController extends Controller
{
    public function entryAction(Request $request)
    {
        $service = new MobileViewModelService();
    	return $this->render('MobileAppBundle::index.html.twig',
            $this->getViewParms(
                array(
                    'tabs' => $service->getTabsData(),
                    'projects' => $service->getProjectsData(),
                    'projectsHeader' => text(2904)
                )
            )
        );
    }

    public function hierarchyAction(Request $request)
    {
        $viewService = new MobileViewModelService();
        $dataService = new MobileDataService(0);
        return $this->render('MobileAppBundle::hierarchy.html.twig',
            $this->getViewParms( array_merge(
                array(
                    'tabs' => $viewService->getTabsData(),
                ),
                $dataService->getWikiHierarchy($request->get('className'), $request->get('objectId'))
            ))
        );
    }

    public function formAction(Request $request)
    {
        $viewService = new MobileViewModelService();
        return $this->render('MobileAppBundle::form.html.twig',
            $this->getViewParms(
                $viewService->getFormData(
                    $request->get('className'), $request->get('objectId'), $request->get('project')
                )
            )
        );
    }

    public function whatsnewAction(Request $request)
    {
        $service = new MobileDataService($request->get('last'));
        return new JsonResponse($service->getWhatsNewCards());
    }

    public function workitemsAction(Request $request)
    {
        $service = new MobileDataService($request->get('last'));
        return new JsonResponse($service->getWorkCards());
    }

    public function commentsAction(Request $request)
    {
        $service = new MobileDataService($request->get('last'));
        return new JsonResponse($service->getDiscussionCards());
    }

    public function peopleAction(Request $request)
    {
        $service = new MobileDataService(0);
        return new JsonResponse($service->getPeopleCards());
    }

    public function projectsAction(Request $request)
    {
        $service = new MobileDataService(0);
        return new JsonResponse($service->getProjectsCards());
    }

    public function buildsAction(Request $request)
    {
        $service = new MobileDataService($request->get('last'));
        return new JsonResponse($service->getBuildCards());
    }

    public function testsAction(Request $request)
    {
        $service = new MobileDataService($request->get('last'));
        return new JsonResponse($service->getTestCards());
    }

    public function wikiAction(Request $request)
    {
        $service = new MobileDataService($request->get('last'));
        return new JsonResponse($service->getWikiCards($request->get('className'), '/mobile/hierarchy'));
    }

    public function formProcessAction(Request $request)
    {
        $service = new MobilePersistService($request->get('project'));
        return new JsonResponse(
            $service->storeData(
                $request->get('className'),
                $request->get('objectId'),
                \JsonWrapper::decode($request->getContent())
            )
        );
    }

    public function formDismissAction(Request $request)
    {
        $service = new MobilePersistService();
        return new JsonResponse(
            $service->dismissNotification(
                $request->get('className'),
                $request->get('objectId')
            )
        );
    }

    public function commentProcessAction(Request $request)
    {
        $service = new MobilePersistService();
        return new JsonResponse(
            $service->storeComment(
                $request->get('className'),
                $request->get('objectId'),
                \JsonWrapper::decode($request->getContent())
            )
        );
    }

    protected function getViewParms( $parms )
    {
        return array_merge(
            array(
                'title' => getFactory()->getObject('cms_SystemSettings')->getAll()->get('Caption'),
                'language' => strtolower(getSession()->getLanguageUid()),
                'app_version' => md5($_SERVER['APP_VERSION'])
            ),
            $parms
        );
    }
}