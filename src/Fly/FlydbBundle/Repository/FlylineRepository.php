<?php

namespace Fly\FlydbBundle\Repository;

use Doctrine\ORM\EntityRepository;

class FlylineRepository extends EntityRepository
{
    public function getLatestFlylines($limit = null)
    {
        $qb = $this->createQueryBuilder('f')
                   ->select('f')
                   ->addOrderBy('f.created', 'DESC');

        if (false === is_null($limit))
            $qb->setMaxResults($limit);

        return $qb->getQuery()
                  ->getResult();
    }
    
}
