<?php

namespace Devprom\ServiceDeskBundle\Repository;
use Devprom\ServiceDeskBundle\Entity\Issue;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Locale\Exception\NotImplementedException;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class IssueRepository extends EntityRepository {

    /**
     * @return QueryBuilder
     */
    protected function getBaseQuery() {
        return $this->_em->createQueryBuilder()->select(array('issue', 'state', 'w.email'))
            ->from('Devprom\\ServiceDeskBundle\\Entity\\Issue', 'issue')
            ->leftJoin('issue.severity', 'severity')
            ->leftJoin('issue.product', 'product')
            ->leftJoin('issue.assignedTo', 'assignee')
            ->leftJoin('issue.stateComment', 'IssueStateComment')
            ->leftJoin('issue.comments', 'comments',
                'WITH',
                'comments.objectClass = \'request\'')
            ->join('issue.state', 'state',
                'WITH',
                'state.objectClass = \'request\' AND state.vpd = issue.vpd')
            ->join('Devprom\\ServiceDeskBundle\\Entity\\Watcher', 'w',
                'WITH',
                'w.vpd = issue.vpd AND w.objectId = issue.id AND w.objectClass = \'request\'');
    }

    /**
     * @return Issue
     */
    public function find($id, $lockMode = LockMode::NONE, $lockVersion = null)
    {
        $qb = $this->getBaseQuery();
        $issue = $qb
            ->addSelect("comments")
            ->andWhere(
                $qb->expr()->eq('issue.id', '?1'))
            ->setParameter(1, $id)
            ->getQuery()
            ->getResult();
        $issue = $this->mapMixedDQLResult($issue);
        return $issue[0];
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return $this->mapMixedDQLResult($this->getBaseQuery()->getQuery()->getResult());
    }

    /**
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        throw new NotImplementedException("Default implementation of this method will return partial object.
         Please use one of specific methods to retrieve Issue");
    }

    public function findByAuthor($authorEmail, $orderBy = null, $limit = null, $offset = null) {
        /** @var QueryBuilder $qb */
        $qb = $this->getBaseQuery();
        $qb->andWhere('w.email = ?1')->setParameter(1, $authorEmail);

        foreach ($orderBy as $column => $direction)
        {
            $qb->addOrderBy($column, $direction);
        }

        $issues = $qb->setMaxResults($limit)->setFirstResult($offset)->getQuery()->getResult();
        $issues = $this->mapMixedDQLResult($issues);
        return $issues;
    }

    public function findByCompany($authorEmail, $orderBy = null, $limit = null, $offset = null) {
        /** @var QueryBuilder $qb */
        $qb = $this->getBaseQuery();
        $qb->andWhere(
        		'w.email IN (SELECT u2.email FROM Devprom\\ServiceDeskBundle\\Entity\\User u1, Devprom\\ServiceDeskBundle\\Entity\\User u2 '.
        		'			  WHERE u1.email = ?1 AND u1.company = u2.company)')->setParameter(1, $authorEmail);

        foreach ($orderBy as $column => $direction)
        {
            $qb->addOrderBy($column, $direction);
        }

        $issues = $qb->setMaxResults($limit)->setFirstResult($offset)->getQuery()->getResult();
        $issues = $this->mapMixedDQLResult($issues);
        return $issues;
    }
    
    /**
     * @return Issue
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        throw new NotImplementedException("Default implementation of this method will return partial object.
         Please use one of specific methods to retrieve Issue");
    }

    /**
     * @param $issues
     * @return array
     */
    protected function mapMixedDQLResult($issues)
    {
        $issues = array_map(function ($val) {
            $val[0]->setAuthorEmail($val['email']);
            return $val[0];
        }, $issues);
        return $issues;
    }

}