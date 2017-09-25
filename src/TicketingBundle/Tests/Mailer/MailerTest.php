<?php
namespace TicketingBundle\Tests\Mailer;

use TicketingBundle\Entity\Order;
use TicketingBundle\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use TicketingBundle\Services\Mailer;
use Symfony\Component\DependencyInjection\ContainerInterface;


class MailerTest extends KernelTestCase{

    private $mailer;


    public function setUp()
    {
        self::bootKernel();

        $this->mailer = self::$kernel->getContainer()
            ->get('ticketing.mail.swiftmailer');
    }

   /* public function __construct(Mailer\MailBySwiftmailer $mailer){
        $this->mailer = $mailer;
    }*/

    public function testSendTicketsbyMail(){


        $bookingDate = new \DateTime("now");
        $birthdayDate = new \DateTime('13-0-1981');

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
        $recipient = 'mickael@parlow-co.com';


        $this->mailer->mailTickets($order, $recipient);


        $this->assertEquals(1,1);
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->mailer = null; // avoid memory leaks
    }

}