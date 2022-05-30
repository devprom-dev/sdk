<?php
namespace Devprom\ApplicationBundle\Service\Project;

use Caxy\Tests\HtmlDiff\Performance\PerformanceTest;
include_once SERVER_ROOT_PATH . "pm/classes/project/ProjectAccessibleActive.php";
include_once SERVER_ROOT_PATH . "core/classes/project/persisters/ProjectGroupPersister.php";

class ProjectNavigatorTreeService
{
    function getTreeGridJsonView()
    {
        $items = array();

        $portfolioIt = getFactory()->getObject('Portfolio')->getAll();
        $visibleVpds = getSession()->getAccessibleVpds();

        $portfolioIt->moveTo('CodeName', 'my');
        $linkToMy = $portfolioIt->get('CodeName') == 'my';

        $projectIt = getFactory()->getObject('ProjectAccessibleActive')
            ->getRegistry()->Query(
                    array(
                        new \ProjectLinksPersister(),
                        new \ProjectGroupPersister()
                    )
                );
        $projectIds = $projectIt->idsToArray();

        $projectIt->moveFirst();
        while( !$projectIt->end() ) {
            $itemId = 'Project'.$projectIt->getId();
            $isProgram = $projectIt->IsProgram();

            $item = array();
            $item['title'] = '<a href="/pm/'.$projectIt->get('CodeName').'">' . $projectIt->getDisplayName() . '</a>';
            $item['caption'] = $projectIt->getDisplayName();
            $item['key'] = $itemId;
            $item['id'] = $projectIt->getId();
            $item['icon'] = false;
            $item['code'] = $projectIt->get('CodeName');

            if ( $isProgram ) {
                $item['extraClasses'] = 'program';
                $item['title'] = '<i class="icon-folder-close"></i> ' . $item['title'];
            }
            else {
                if( !in_array($projectIt->get('VPD'), $visibleVpds) ) {
                    // display projects related to user only
                    $projectIt->moveNext();
                    continue;
                }
                $item['extraClasses'] = 'project';
            }

            $parents = array();
            foreach( \TextUtils::parseIds($projectIt->get('Programs')) as $programId ) {
                if ( in_array($programId, $projectIds) ) {
                    $parents[] = 'Project'.$programId;
                }
            }
            foreach( \TextUtils::parseIds($projectIt->get('GroupId')) as $portfolioId ) {
                $dbId = 10000000 + $portfolioId;
                $portfolioIt->moveToId($dbId);
                if ( $portfolioIt->getId() != '' ) {
                    $parents[] = 'Portfolio'.($dbId);
                }
            }
            if( count($parents) < 1 && !$isProgram ) {
                if ( $linkToMy ) {
                    $portfolioIt->moveTo('CodeName', 'my');
                    $parents[] = 'Portfolio' . $portfolioIt->getId();
                }
                else {
                    $portfolioIt->moveTo('CodeName', 'all');
                    $parents[] = 'Portfolio' . $portfolioIt->getId();
                }
            }
            $item['parent'] = join(',',$parents);
            $items[] = $item;

            $projectIt->moveNext();
        }

        $portfolioIt->moveFirst();
        while( !$portfolioIt->end() ) {
            $itemId = 'Portfolio'.$portfolioIt->getId();

            $item = array();
            $item['title'] = '<i class="icon-briefcase"></i> <a href="/pm/'.$portfolioIt->get('CodeName').'">'
                . $portfolioIt->getDisplayName() . '</a>';
            $item['caption'] = $portfolioIt->getDisplayName();
            $item['key'] = $itemId;
            $item['id'] = $portfolioIt->getId();
            $item['icon'] = false;
            $item['parent'] = '';
            $item['code'] = $portfolioIt->get('CodeName');

            $items[] = $item;

            $portfolioIt->moveNext();
        }

        $tree = \JsonWrapper::buildJSONTree($items, '');
        $recentProject = $this->getRecentProjectCodeName();

        // skip portfolios where there are no children (except 'my' and 'all' portfolios)
        foreach( $tree as $key => $parent ) {
            if ( strpos($parent['key'], 'Portfolio') === false ) continue;
            if ( in_array($parent['code'], array('all', 'my')) ) continue;
            if ( count($parent['children']) < 1 ) unset($tree[$key]);

            foreach( $parent['children'] as $childrenId => $children ) {
                if ( $recentProject != '' && $children['code'] == $recentProject ) {
                    $tree[$key]['children'][$childrenId]['focus'] = true;
                    $tree[$key]['children'][$childrenId]['selected'] = true;
                    $tree[$key]['children'][$childrenId]['expanded'] = true;
                    $tree[$key]['children'][$childrenId]['active'] = true;
                    $recentProject = '';
                }
            }
        }

        return array_values($tree);
    }

    function getRecentProjectCodeName()
    {
        $registry = getFactory()->getObject('pm_Participant')->getRegistryBase();
        $registry->setLimit(1);
        return $registry->Query(
                    array (
                        new \FilterAttributePredicate('SystemUser', getSession()->getUserIt()->getId()),
                        new \SortRecentModifiedClause(),
                        new \SortProjectCaptionClause(),
                        new \EntityProjectPersister()
                    )
                )->get('ProjectCodeName');
   }
}