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
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $useKnowledgeBase = $this->container->getParameter('use_knowledgebase');
        if ( !$useKnowledgeBase ) {
            return $this->redirect($this->generateUrl('issue_list'));
        }

        $service = $this->get('knowledge_service');
        $vpds = $this->container->getParameter('supportProjectVpds');

        $themes = $service->getThemes($vpds);
        if ( count($themes) < 1 ) {
            return $this->redirect($this->generateUrl('issue_list'));
        }

        return array(
            'roots' => $service->getRoots($vpds),
            'themes' => $themes,
            'articles' => $service->getArticles($vpds)
        );
    }

    /**
     * @Route("/docs/{article}", name="docs_article")
     * @Method("GET")
     * @Template()
     */
    public function showAction($article)
    {
        $useKnowledgeBase = $this->container->getParameter('use_knowledgebase');
        if ( !$useKnowledgeBase ) {
            return $this->redirect($this->generateUrl('issue_list'));
        }

        $service = $this->get('knowledge_service');
        $article = html_entity_decode($article);
        $articles = $service->getArticles($this->container->getParameter('supportProjectVpds'));
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
     * @Template()
     */
    public function searchAction( Request $request )
    {
        $useKnowledgeBase = $this->container->getParameter('use_knowledgebase');
        if ( !$useKnowledgeBase ) {
            return $this->redirect($this->generateUrl('issue_list'));
        }

        $searchQuery = $request->get('q');
        if ( $searchQuery == '' ) {
            return $this->redirect($this->generateUrl('docs_list'));
        }

        $service = $this->get('knowledge_service');
        $themes = $service->getThemes($this->container->getParameter('supportProjectVpds'));
        $articles = $service->searchArticles($themes, $searchQuery, $request->getLocale());

        if ( count($articles) == 1 ) {
            return $this->redirect($this->generateUrl('docs_article', array('article' => $articles[0]->getName())));
        }
        return array(
            'query' => $searchQuery,
            'articles' => $articles
        );
    }
}