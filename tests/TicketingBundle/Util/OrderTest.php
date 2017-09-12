<?php

namespace Tests\TicketingBundle\Util;

use TicketingBundle\Entity\Order;
use TicketingBundle\Entity\Ticket;
use PHPUnit\Framework\TestCase;
include_once __DIR__.'/../../../app/autoload.php';
include_once __DIR__.'/../../../var/bootstrap.php.cache';

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

    //Free (<4)
    public function testTicketPrice(){

        $ticket = new Ticket();
        $ticket->setBirthdayDate(new \DateTime());
        //Reduced price
        $ticket->setReducedPrice(1);
        //Full-day
        $ticket->setType(1);
        $ticket->setTicketPrice();
        $result = $ticket->getPrice();

        $this->assertEquals(0, $result);
    }
    //Reduced price for adult (>12 - <60)
    public function testTicketPrice2(){

        $ticket = new Ticket();
        $ticket->setBirthdayDate(new \DateTime('1981-01-01'));
        //Reduced price
        $ticket->setReducedPrice(1);
        //full day
        $ticket->setType(1);
        $ticket->setTicketPrice();
        $result = $ticket->getPrice();

        $this->assertEquals(10, $result);
    }

    //Senior price (>60)
    public function testTicketPrice3(){

        $ticket = new Ticket();
        $ticket->setBirthdayDate(new \DateTime('1950-01-01'));
        //No reduced price
        $ticket->setReducedPrice(0);
        //Full-day
        $ticket->setType(1);
        $ticket->setTicketPrice();
        $result = $ticket->getPrice();

        $this->assertEquals(12, $result);
    }

    //Child price (4-12)
    public function testTicketPrice4(){

        $ticket = new Ticket();
        $ticket->setBirthdayDate(new \DateTime('2010-01-01'));
        //No reduced price
        $ticket->setReducedPrice(0);
        //Full-day
        $ticket->setType(1);
        $ticket->setTicketPrice();
        $result = $ticket->getPrice();

        $this->assertEquals(8, $result);
    }

    //Half-day price
    public function testTicketPrice5(){

        $ticket = new Ticket();
        $ticket->setBirthdayDate(new \DateTime('1981-01-01'));
        //No reduced price
        $ticket->setReducedPrice(0);
        //Half-day
        $ticket->setType(0);
        $ticket->setTicketPrice();
        $result = $ticket->getPrice();

        $this->assertEquals(10, $result);
    }

}