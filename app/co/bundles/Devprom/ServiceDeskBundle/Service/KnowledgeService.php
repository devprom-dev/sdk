<?php

namespace Devprom\ServiceDeskBundle\Service;
include_once SERVER_ROOT_PATH."ext/locale/LinguaStemRu.php";

use Devprom\ServiceDeskBundle\Entity\Issue;
use Devprom\ServiceDeskBundle\Entity\IssueComment;
use Devprom\ServiceDeskBundle\Entity\IssueState;
use Devprom\ServiceDeskBundle\Entity\IssueStateComment;
use Devprom\ServiceDeskBundle\Entity\Priority;
use Devprom\ServiceDeskBundle\Entity\User;
use Devprom\ServiceDeskBundle\Entity\Watcher;
use Devprom\ServiceDeskBundle\Mailer\Mailer;
use Devprom\ServiceDeskBundle\Repository\IssueRepository;
use Devprom\ServiceDeskBundle\Util\TextUtil;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class KnowledgeService
{
    /** @var  EntityManager */
    private $em;

    function __construct($em) {
        $this->em = $em;
    }

    function getThemes( $vpds ) {
        $repository = $this->em->getRepository('DevpromServiceDeskBundle:KnowledgeBase');
        return $repository->findBy(
            array(
                'parent' => $this->getRoots($vpds)
            ),
            array(
                'sortIndex' => 'ASC'
            )
        );
    }

    function getArticles($vpds) {
        $repository = $this->em->getRepository('DevpromServiceDeskBundle:KnowledgeBase');
        return $repository->findBy(
            array(
                'parent' => $this->getThemes($vpds)
            ),
            array(
                'sortIndex' => 'ASC'
            )
        );
    }

    function getRoots( $vpds ) {
        $repository = $this->em->getRepository('DevpromServiceDeskBundle:KnowledgeBase');
        return $repository->createQueryBuilder('t')
            ->where('
                t.vpd IN (:vpdarray) AND t.parent IS NULL AND t.referenceName = 1 
                AND EXISTS (SELECT 1 FROM \Devprom\ServiceDeskBundle\Entity\KnowledgeBase c WHERE c.parent = t.id) 
                ')
            ->setParameter('vpdarray', $vpds)
            ->getQuery()
            ->getResult();
    }

    function searchArticles( $themes, $query, $locale = 'en' )
    {
        $searchItems = \SearchRules::getSearchItems(\DAL::Instance()->Escape($query), $locale);
        $anyWords = array_map(
            function($word) {
                return $word.'*';
            },
            $searchItems
        );
        $allWords = array_map(
            function($word) {
                return '+'.$word;
            },
            $anyWords
        );

        $rsm = new ResultSetMappingBuilder($this->em);
        $rsm->addRootEntityFromClassMetadata('\Devprom\ServiceDeskBundle\Entity\KnowledgeBase', 'e');
        $sql = "
                SELECT " . $rsm->generateSelectClause(array('e' => 't')) . "
                  FROM WikiPage t
                 WHERE t.ParentPage IN (:themes) 
                   AND t.ReferenceName = 1
                   AND (MATCH (t.Content) AGAINST (:search IN BOOLEAN MODE) OR t.Caption REGEXP :regexp) 
            ";

        $items = $this->em->createNativeQuery($sql, $rsm)
            ->setParameter('themes', $themes)
            ->setParameter('search', join(' ',$allWords))
            ->setParameter('regexp', join('|',$searchItems))
            ->getResult();

        if ( count($items) < 1 ) {
            $items = $this->em->createNativeQuery($sql, $rsm)
                ->setParameter('themes', $themes)
                ->setParameter('search', join(' ',$anyWords))
                ->setParameter('regexp', join('|',$searchItems))
                ->getResult();
        }

        return $items;
    }
}