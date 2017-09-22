<?php

namespace TicketingBundle\Mailer;
use TicketingBundle\Entity\Order;

class MailBySwiftmailer{

    private $mailer;
    private $twig;
    private $from;

    public function  __construct(\Swift_Mailer $mailer,\Twig_Environment $twig,String $from){

        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->from =$from;
    }

    public function mailTickets(Order $order, String $recipient)
    {

        $body = $this->renderTemplateHTML($order);
        $part = $this->renderTemplateText($order);
        

        $message = \Swift_Message::newInstance()
            ->setSubject('ticketing.email.credential')
            ->setFrom($this->from)
            ->setTo($recipient)
            ->setBody($body,'text/html'
            )
            ->addPart($part,'text/plain');
        $this->mailer->send($message);
    }

    public function renderTemplateHTML(Order $order)
    {
        return $this->twig->render(
            'TicketingBundle:Emails:Etickets.html.twig',
            array(
                'order' => $order
            )
        );
    }

    public function renderTemplateText(Order $order)
    {
        return $this->twig->render(
            'TicketingBundle:Emails:Etickets.text.twig',
            array(
                'order' => $order
            )
        );
    }

}