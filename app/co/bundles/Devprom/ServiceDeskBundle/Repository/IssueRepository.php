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
class IssueRepository extends EntityRepository
{
    private $pageSize = 50;

    /**
     * @return QueryBuilder
     */
    protected function getBaseQuery() {
        return $this->_em->createQueryBuilder()->select(array('issue', 'state'))
            ->from('Devprom\\ServiceDeskBundle\\Entity\\Issue', 'issue')
            ->leftJoin('issue.severity', 'severity')
            ->leftJoin('issue.customer', 'customer')
            ->leftJoin('issue.author', 'author')
            ->leftJoin('issue.product', 'product')
            ->leftJoin('issue.project', 'project')
            ->leftJoin('issue.assignedTo', 'assignee')
            ->leftJoin('issue.stateComment', 'IssueStateComment')
            ->leftJoin('issue.comments', 'comments',
                'WITH',
                'comments.objectClass IN(\'request\',\'issue\')')
            ->leftJoin('issue.state', 'state',
                'WITH',
                'state.objectClass IN (\'request\',\'issue\') AND state.vpd = issue.vpd');
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
        return $issue[0];
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return $this->getBaseQuery()->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        throw new NotImplementedException("Default implementation of this method will return partial object.
         Please use one of specific methods to retrieve Issue");
    }

    public function findByAuthor($authorEmail, $orderBy = null, $state = '', $page = 1)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->getBaseQuery();
        $qb->andWhere('(customer.email = ?1 or author.email = ?1) and state.terminal IN (?2)')
            ->setParameter(1, $authorEmail)
            ->setParameter(2, in_array($state, array('','all')) ? array('N','Y','I') : ($state == 'open' ? array('N','I') : array('Y')));

        foreach ($orderBy as $column => $direction) {
            $qb->addOrderBy($column, $direction);
        }

        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($qb);
        $pagesCount = ceil(count($paginator) / $this->pageSize);

        // now get one page's items:
        $paginator
            ->getQuery()
            ->setFirstResult($this->pageSize * ($page-1))
            ->setMaxResults($this->pageSize);

        return [$paginator, $pagesCount];
    }

    public function findByCompany($authorEmail, $orderBy = null, $state = '', $page = 1) {
        /** @var QueryBuilder $qb */
        $qb = $this->getBaseQuery();
        $qb->andWhere(
        		'EXISTS (SELECT 1 FROM Devprom\\ServiceDeskBundle\\Entity\\User u1, Devprom\\ServiceDeskBundle\\Entity\\User u2 '.
        		'			  WHERE (u2.email = customer.email or u2.email = author.email) and u1.email = ?1 AND u1.company = u2.company) and state.terminal IN (?2)')
            ->setParameter(1, $authorEmail)
            ->setParameter(2, in_array($state, array('','all')) ? array('N','Y','I') : ($state == 'open' ? array('N','I') : array('Y')));

        foreach ($orderBy as $column => $direction) {
            $qb->addOrderBy($column, $direction);
        }

        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($qb);
        $pagesCount = ceil(count($paginator) / $this->pageSize);

        // now get one page's items:
        $paginator
            ->getQuery()
            ->setFirstResult($this->pageSize * ($page-1))
            ->setMaxResults($this->pageSize);

        return [$paginator, $pagesCount];
    }
    
    /**
     * @return Issue
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        throw new NotImplementedException("Default implementation of this method will return partial object.
         Please use one of specific methods to retrieve Issue");
    }
}