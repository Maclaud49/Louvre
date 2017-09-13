<?php

namespace TicketingBundle\Stripe;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Templating\EngineInterface;
use TicketingBundle\Entity\Order;
use TicketingBundle\TicketingBundle;

class PaiementStripe{

    //EntityManagerInterface
    private $em;
    //Mailer
    private $mailer;
    //Template
    private $templating;

    public function  __construct(EntityManagerInterface $em, \Swift_Mailer $mailer, EngineInterface $templating){

        $this->em = $em;
        $this->mailer = $mailer;
        $this->templating = $templating;

    }

    public function paiementByStripe(Order $order)
    {
        \Stripe\Stripe::setApiKey('sk_test_my7udfLsv4JQA7TK3BqBDyTk');

        // Get the credit card details submitted by the form
        $token = $_POST['stripeToken'];
        $amount = $order->getOrderAmount();

        // Create a charge: this will charge the user's card
            $charge = \Stripe\Charge::create(array(
                "amount" => $amount * 100, // Amount in cents
                "currency" => "eur",
                "source" => $token,
                "description" => "Paiement Stripe"
            ));
    }


        public function mailTickets(Order $order)
        {
            $recipient = $_POST['stripeEmail'];

            $message = \Swift_Message::newInstance()
                ->setSubject('Justificatif pour votre visite du musée du Louvre')
                ->setFrom('billet.simple.alaska@gmail.com')
                ->setTo($recipient)
                ->setBody($this->templating->render('TicketingBundle:Emails:Etickets.html.twig', array('order' => $order)),'text/html'
                );
            $this->mailer->send($message);
        }

        public function saveOrder(Order $order)
        {
                    $this->em->persist($order);
                    $this->em->flush();
        }

}