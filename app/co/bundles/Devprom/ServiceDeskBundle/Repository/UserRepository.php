<?php

namespace Devprom\ServiceDeskBundle\Repository;
use Devprom\ServiceDeskBundle\Entity\User;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Locale\Exception\NotImplementedException;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class UserRepository extends EntityRepository {

    public function isCollegues($email_left, $email_right)
    {
        $qb = $this->_em->createQueryBuilder()
	        	->select('u1.id')
	        	->from('Devprom\\ServiceDeskBundle\\Entity\\User', 'u1')
	        	->join('Devprom\\ServiceDeskBundle\\Entity\\User', 'u2', \Doctrine\ORM\Query\Expr\Join::WITH, 'u1.company = u2.company')
	        	->where('u1.email = ?1 AND u2.email = ?2')
	        	->setParameters(array(1 => $email_left, 2 => $email_right));

        return count($qb->getQuery()->getResult()) > 0;
    }
}