<?php


namespace TicketingBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use TicketingBundle\Entity\Order;
use TicketingBundle\Entity\Ticket;

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

    public function orderAmount(Order $order){

        $tickets = $order->getTickets();
        $orderAmount = 0;
         foreach ($tickets as $ticket){
             $orderAmount += $ticket->getPrice();
         }


        return $orderAmount;
    }

}