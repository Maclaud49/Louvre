<?php


namespace TicketingBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class OrderRepository extends EntityRepository
{

    public function generatedCodeCheck($code)
    {
        $qb = $this->createQueryBuilder('a');

        $qb
            ->where('a.bookingCode = :code')
            ->setParameter('code', $code);


        return $qb
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function SoldTicketsNb(\DateTime $date){
        $qb = $this->createQueryBuilder('a');

        $qb
            ->select('sum(a.quantity)')
            ->where('a.bookingDate = :date')
            ->setParameter('date', $date);


        return $qb
            ->getQuery()
            ->getSingleScalarResult();
    }

}