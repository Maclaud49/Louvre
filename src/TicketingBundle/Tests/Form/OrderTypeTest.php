<?php

namespace TicketingBundle\Tests\Form;

use TicketingBundle\Form\OrderType;
use TicketingBundle\Entity\Order;
use TicketingBundle\Entity\Ticket;
use Symfony\Component\Form\Test\TypeTestCase;

class OrderTypeTest extends TypeTestCase
{
public function testSubmitValidData()
{
    $bookingDate = new \DateTime("now");
    $birthdayDate = new \DateTime('13-04-1981');

$formDataTicket = array(
    'type'=>'1',
    'firstName'=>'Mickaël',
    'lastName'=>'Parlow',
    'country'=>'France',
    'birthdayDate'=>$birthdayDate,
    'reducedPrice'=> false
);

$ticket = new Ticket();
$ticket->fromArray($formDataTicket);

$formDataOrder = array(
    'bookingDate' => $bookingDate,
    'quantity' => '1',
    'tickets' => $ticket
);

$order = new Order();
$order->fromArray($formDataOrder);

$form = $this->factory->create(OrderType::class,$order);

$form->submit($formDataOrder);

$this->assertTrue($form->isSynchronized());
$this->assertEquals($order, $form->getData());

$view = $form->createView();
$children = $view->children;

foreach (array_keys($formDataOrder) as $key) {
$this->assertArrayHasKey($key, $children);
}
}
}