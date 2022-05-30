<?php
namespace Devprom\ServiceDeskBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/")
 */
class KnowledgeController extends Controller
{
    /**
     * @Route("/", name="docs_list")
     * @Method("GET")
     * @Template("DevpromServiceDeskBundle:Knowledge:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $service = $this->get('knowledge_service');
        $vpds = $this->getProjectVpds();

        $themes = $service->getThemes($vpds);
        if ( count($themes) < 1 ) {
            return $this->redirect($this->generateUrl('issue_list'));
        }

        $articles = $service->getArticles($vpds);
        foreach( $themes as $key => $theme ) {
            $found = array_filter($articles, function($value) use($theme) {
                return $value->getParent() == $theme;
            });
            $theme->setSingle(count($found) < 1);
        }

        return array(
            'roots' => $service->getRoots($vpds),
            'themes' => $themes,
            'articles' => $articles
        );
    }

    /**
     * @Route("/docs/{article}", name="docs_article", requirements={"article"=".+"})
     * @Method("GET")
     * @Template("DevpromServiceDeskBundle:Knowledge:show.html.twig")
     */
    public function showAction($article)
    {
        $service = $this->get('knowledge_service');
        $article = html_entity_decode($article);
        $articles = $service->getArticles($this->getProjectVpds());
        if ( count($articles) < 1 ) {
            return $this->redirect($this->generateUrl('issue_list'));
        }

        foreach( $articles as $articleEntity ) {
            if ( stripslashes(html_entity_decode($articleEntity->getName())) == $article ) {
                return array(
                    'article' => $articleEntity,
                    'articles' => $articles
                );
            }
        }
        return $this->redirect($this->generateUrl('docs_list'));
    }

    /**
     * @Route("/search", name="docs_search")
     * @Method("GET")
     * @Template("DevpromServiceDeskBundle:Knowledge:search.html.twig")
     */
    public function searchAction( Request $request )
    {
        $searchQuery = $request->get('q');
        if ( $searchQuery == '' ) {
            return $this->redirect($this->generateUrl('docs_list'));
        }

        $service = $this->get('knowledge_service');
        $themes = $service->getThemes($this->getProjectVpds());
        $articles = $service->searchArticles($themes, $searchQuery, $request->getLocale());

        if ( count($articles) < 1 ) {
            return $this->redirect($this->generateUrl('issue_list'));
        }
        if ( count($articles) == 1 ) {
            return $this->redirect($this->generateUrl('docs_article', array('article' => $articles[0]->getName())));
        }

        return array(
            'query' => $searchQuery,
            'articles' => $articles
        );
    }

    protected function getProjectVpds()
    {
        $customer_vpds = array();
        if ( is_object($this->getUser()) ) {
            if ( $this->getUser()->getCompany() ) {
                foreach($this->getUser()->getCompany()->getProjects() as $project_ref) {
                    $customer_vpds[] = $project_ref->getProject()->getVpd();
                }
            }
            if ( count($customer_vpds) < 1 ) {
                $customer_vpds = $this->container->getParameter('commonProjectVpds');
            }
        }
        return array_merge( $customer_vpds,
            $this->container->getParameter('publicKBProjectVpds')
        );
    }
}