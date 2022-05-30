<?php
namespace Devprom\ProjectBundle\Controller;
use Devprom\CommonBundle\Controller\PageController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Devprom\ProjectBundle\Service\TreeGridViewModel\TreeGridService;

include_once SERVER_ROOT_PATH."pm/views/ui/Common.php";
include_once SERVER_ROOT_PATH."pm/views/design/ComponentsPage.php";

class ComponentController extends PageController
{
    public function indexAction(Request $request)
    {
        if ( $request->get('report') != '' ) {
            $_REQUEST['report'] = $request->get('report');
        }
        return $this->responsePage( new \ComponentsPage() );
    }

    public function chartAction(Request $request)
    {
        $_REQUEST['view'] = 'chart';
        return $this->responsePage( new \ComponentsPage() );
    }

    public function traceAction(Request $request)
    {
        $_REQUEST['view'] = 'trace';
        return $this->responsePage( new \ComponentsPage() );
    }

    public function treeAction(Request $request)
    {
        $page = new \ComponentsPage();
        $page->getTableRef()->buildFilters();

        $list = new \ComponentList($page->getTableRef()->getObject());
        $list->skipInvisiblePersisters(false);
        $list->setTable($page->getTableRef());

        $service = new TreeGridService();
        return new JsonResponse(
            $service->getTreeGridJsonView($list,
                $this->getTemplatingEngine(),
                'Caption',
                'ChildrenCount',
                'ParentComponent'
            )
        );
    }
}