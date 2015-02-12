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
	        	->select('u')
	        	->from('Devprom\\ServiceDeskBundle\\Entity\\User', 'u')
	        	->where('u.email = ?1')
	        	->andWhere('u.company IN (SELECT u1.company FROM Devprom\\ServiceDeskBundle\\Entity\\User u1 WHERE u1.email = ?2)')
	        	->setParameter(1, $email_left)
	        	->setParameter(2, $email_right);

        return count($qb->getQuery()->getResult()) > 0;
    }
}