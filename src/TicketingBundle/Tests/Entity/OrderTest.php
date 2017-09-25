<?php

namespace TicketingBundle\Tests\Entity;

use TicketingBundle\Entity\Order;
use TicketingBundle\Entity\Ticket;
use PHPUnit\Framework\TestCase;



class OrderTest extends TestCase{

    public function testOrderAmount(){

        // 2 adults with 1 reduced price (16 + 10)
        $order = new Order();
        $ticket1 = new Ticket();
        //>12 years old
        $ticket1->setBirthdayDate(new \DateTime('2000-01-01'));
        //full day
        $ticket1->setType(1);
        //No reduced price
        $ticket1->setReducedPrice(0);
        $ticket1->setTicketPrice();
        $ticket2 = new Ticket();
        //>12 years old
        $ticket2->setBirthdayDate(new \DateTime('2002-01-01'));
        //full day
        $ticket1->setType(1);
        //Reduced price
        $ticket1->setReducedPrice(1);
        $ticket2->setTicketPrice();
        $order->addTicket($ticket1);
        $order->addTicket($ticket2);
        $orderAmount=0;
        foreach ($order->getTickets() as $ticket){
            $orderAmount += $ticket->getPrice();
        }
        $order->setOrderAmount($orderAmount);
        $result = $order->getOrderAmount();

        $this->assertEquals(26,$result);
    }





}