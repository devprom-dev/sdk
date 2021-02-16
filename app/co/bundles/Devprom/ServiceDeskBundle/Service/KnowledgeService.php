<?php

namespace Devprom\ServiceDeskBundle\Service;
include_once SERVER_ROOT_PATH."ext/locale/LinguaStemRu.php";

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use FOS\UserBundle\Model\UserInterface;

class KnowledgeService
{
    /** @var  EntityManager */
    private $em;
    private $tokenStorage;

    function __construct($em, $tokenStorage) {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    function getThemes( $vpds )
    {
        $restricted = $this->restrictedArticles($vpds);
        $repository = $this->em->getRepository('DevpromServiceDeskBundle:KnowledgeBase');
        return $repository->createQueryBuilder('t')
            ->where('t.parent IN (:parents) AND t.id NOT IN (:restricted)')
            ->setParameter('parents', $this->getRoots($vpds))
            ->setParameter('restricted', $restricted)
            ->orderBy('t.sortIndex')
            ->getQuery()
            ->getResult();
    }

    function getArticles($vpds)
    {
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

    function getRoots( $vpds )
    {
        $user = $this->tokenStorage->getToken()->getUser();

        $restricted = $this->restrictedArticles($vpds);
        $repository = $this->em->getRepository('DevpromServiceDeskBundle:KnowledgeBase');

        return $repository->createQueryBuilder('t')
            ->where('
                t.vpd IN (:vpdarray) AND t.parent IS NULL AND t.referenceName = 1 
                AND EXISTS (SELECT 1 FROM \Devprom\ServiceDeskBundle\Entity\KnowledgeBase c 
                             WHERE c.parent = t.id AND c.id NOT IN (:restricted)) 
                AND EXISTS (SELECT 1 FROM \Devprom\ServiceDeskBundle\Entity\Project p 
                             WHERE p.vpd = t.vpd AND COALESCE(p.knowledgeBaseServiceDesk,\'N\') = \'Y\') '
            )
            ->setParameter('vpdarray', $vpds)
            ->setParameter('restricted', $restricted)
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
                   AND NOT EXISTS (SELECT 1 FROM pm_ObjectAccess a, pm_ProjectRole r
                                    WHERE INSTR(t.ParentPath, CONCAT(',',a.ObjectId,',')) > 0
                                      AND a.ProjectRole = r.pm_ProjectRoleId
                                      AND a.ObjectClass = 'projectpage' AND a.AccessType = 'none'
                                      AND r.ReferenceName = 'guest')
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

    function restrictedArticles( $vpds )
    {
        $rsm = new ResultSetMappingBuilder($this->em);
        $rsm->addRootEntityFromClassMetadata('\Devprom\ServiceDeskBundle\Entity\KnowledgeBase', 'e');
        $sql = "
                SELECT " . $rsm->generateSelectClause(array('e' => 't')) . "
                  FROM WikiPage t
                 WHERE t.VPD IN (:vpds) 
                   AND t.ReferenceName = 1
                   AND EXISTS (SELECT 1 FROM pm_ObjectAccess a, pm_ProjectRole r
                                WHERE INSTR(t.ParentPath, CONCAT(',',a.ObjectId,',')) > 0
                                  AND a.ProjectRole = r.pm_ProjectRoleId
                                  AND a.ObjectClass = 'projectpage' AND a.AccessType = 'none'
                                  AND r.ReferenceName = 'guest')
            ";
        $result = $this->em->createNativeQuery($sql, $rsm)
            ->setParameter('vpds', $vpds)
            ->getResult();
        if ( count($result) < 1 ) return array(0);
        return $result;
    }

    /**
     * @param $attachmentId
     * @return KnowledgeBaseAttachment
     */
    public function getAttachmentById($attachmentId) {
        return $this->em->getRepository("DevpromServiceDeskBundle:KnowledgeBaseAttachment")->find($attachmentId);
    }
}