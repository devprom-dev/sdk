<?php
namespace Devprom\ProjectBundle\Controller;

use Devprom\CommonBundle\Controller\PageController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Devprom\ProjectBundle\Service\TreeviewModel\HierarchyService;
use Devprom\ProjectBundle\Service\TreeviewModel\WikiService;
use Devprom\ProjectBundle\Service\TreeGridViewModel\TreeGridService;
include_once SERVER_ROOT_PATH."pm/views/ui/Common.php";
include_once SERVER_ROOT_PATH."pm/views/product/FunctionsPage.php";
include_once SERVER_ROOT_PATH."pm/views/project/VersionPage.php";

class TreeController extends PageController
{
    public function indexAction(Request $request)
    {
    	if ( $request->get('classname') == "" ) {
    		throw $this->createNotFoundException('Class name is undefined but required');
    	}

        $object = getFactory()->getObject($request->get('classname'));
        if ( $object instanceof \WikiPage ) {
            $service = new WikiService(
                $request->get('classname'),
                $request->get('root'),
                $request->query->has('crossProject')
            );
            return new JsonResponse($service->getData());
        }

    	if ( count($object->getAttributesByGroup('hierarchy-parent')) > 0 ) {
    		$service = new HierarchyService(
                $object,
                $request->get('root'),
                $request->get('selectableClass')
            );
            return new JsonResponse($service->getData());
    	}

    	return new JsonResponse(array());
    }

    public function gridAction(Request $request)
    {
        switch( $request->get('classname') ) {
            case 'feature':
                $page = new \FunctionsPage();
                $page->getTableRef()->buildFilters();

                $list = new \FunctionList($page->getTableRef()->getObject());
                $list->skipInvisiblePersisters(false);
                $list->setTable($page->getTableRef());

                $service = new TreeGridService();
                return new JsonResponse(
                    $service->getTreeGridJsonView($list,
                        $this->getTemplatingEngine(),
                        'Caption',
                        'ChildrenCount',
                        'ParentFeature'
                    )
                );

            case 'requirement':
                getFactory()->getPluginsManager()->useModule('requirements', 'pm', 'hie');

                $page = new \RequirementsPage();
                $page->getTableRef()->buildFilters();

                $list = new \RequirementList($page->getTableRef()->getObject());
                $list->skipInvisiblePersisters(false);
                $list->setTable($page->getTableRef());

                $service = new TreeGridService();
                return new JsonResponse(
                    $service->getTreeGridJsonView($list,
                        $this->getTemplatingEngine(),
                        'Caption',
                        'TotalCount',
                        'ParentPage',
                        $request->get('trace') != '' ? $request->get('trace') : 'trace'
                    )
                );

            case 'requirementdocs':
                getFactory()->getPluginsManager()->useModule('requirements', 'pm', 'docs');
                $_REQUEST['view'] = 'docs';

                $page = new \RequirementsPage();
                $page->getTableRef()->buildFilters();

                $list = new \RequirementRootList($page->getTableRef()->getObject());
                $list->skipInvisiblePersisters(false);
                $list->setTable($page->getTableRef());

                $service = new TreeGridService();
                return new JsonResponse(
                    $service->getTreeGridJsonView($list,
                        $this->getTemplatingEngine(),
                        'Caption',
                        'TotalCount',
                        'ParentPage',
                        'trace-baselines'
                    )
                );

            case 'testplan':
                getFactory()->getPluginsManager()->useModule('testing', 'pm', 'docs');
                $_REQUEST['module'] = 'docs';

                $page = new \TestScenarioPage();
                $page->getTableRef()->buildFilters();

                $list = new \TestingDocsRootList($page->getTableRef()->getObject());
                $list->skipInvisiblePersisters(false);
                $list->setTable($page->getTableRef());

                $service = new TreeGridService();
                return new JsonResponse(
                    $service->getTreeGridJsonView($list,
                        $this->getTemplatingEngine(),
                        'Caption',
                        'TotalCount',
                        'ParentPage',
                        'trace-baselines'
                    )
                );

            case 'testscenario':
                getFactory()->getPluginsManager()->useModule('testing', 'pm', 'docs');

                $page = new \TestScenarioPage();
                $page->getTableRef()->buildFilters();

                $list = new \TestingDocsTree($page->getTableRef()->getObject());
                $list->skipInvisiblePersisters(false);
                $list->setTable($page->getTableRef());

                $service = new TreeGridService();
                return new JsonResponse(
                    $service->getTreeGridJsonView($list,
                        $this->getTemplatingEngine(),
                        'Caption',
                        'TotalCount',
                        'ParentPage',
                        'trace-horizontal'
                    )
                );

            case 'testcaseexecution':
                getFactory()->getPluginsManager()->useModule('testing', 'pm', 'details');

                $page = new \TestResultsDetailedPage();
                $page->getTableRef()->buildFilters();

                $list = new \TestResultsDetailList($page->getTableRef()->getObject());
                $list->skipInvisiblePersisters(false);
                $list->setTable($page->getTableRef());

                $service = new TreeGridService();
                return new JsonResponse(
                    $service->getTreeGridJsonView($list,
                        $this->getTemplatingEngine(),
                        'Caption',
                        'skip',
                        'TestCase',
                        'skip'
                    )
                );

            case 'helppage':
                getFactory()->getPluginsManager()->useModule('helpdocs', 'pm', 'hie');

                $page = new \HelpFilesPage();
                $page->getTableRef()->buildFilters();

                $list = new \HelpPageList($page->getTableRef()->getObject());
                $list->skipInvisiblePersisters(false);
                $list->setTable($page->getTableRef());

                $service = new TreeGridService();
                return new JsonResponse(
                    $service->getTreeGridJsonView($list,
                        $this->getTemplatingEngine(),
                        'Caption',
                        'TotalCount',
                        'ParentPage',
                        'trace-horizontal'
                    )
                );

            case 'helpdocs':
                getFactory()->getPluginsManager()->useModule('helpdocs', 'pm', 'docs');
                $_REQUEST['view'] = 'docs';

                $page = new \HelpFilesPage();
                $page->getTableRef()->buildFilters();

                $list = new \HelpPageRootList($page->getTableRef()->getObject());
                $list->skipInvisiblePersisters(false);
                $list->setTable($page->getTableRef());

                $service = new TreeGridService();
                return new JsonResponse(
                    $service->getTreeGridJsonView($list,
                        $this->getTemplatingEngine(),
                        'Caption',
                        'TotalCount',
                        'ParentPage',
                        'trace-baselines'
                    )
                );

            case 'stage':
                $page = new \VersionPage();
                $page->getTableRef()->buildFilters();

                $list = new \VersionList($page->getTableRef()->getObject());
                $list->skipInvisiblePersisters(false);
                $list->setTable($page->getTableRef());

                $service = new TreeGridService();
                return new JsonResponse(
                    $service->getTreeGridJsonView($list,
                        $this->getTemplatingEngine(),
                        'Caption',
                        'ChildrenCount',
                        'ParentStage',
                        'trace'
                    )
                );

            default:
                throw $this->createNotFoundException('Class name is undefined but required');
        }
    }
}