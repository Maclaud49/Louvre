<?php

namespace TicketingBundle\Tests\Entity;

use TicketingBundle\Entity\Order;
use TicketingBundle\Entity\Ticket;
use PHPUnit\Framework\TestCase;

class TicketTest extends TestCase{

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