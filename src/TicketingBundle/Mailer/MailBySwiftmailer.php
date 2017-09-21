<?php

namespace TicketingBundle\Mailer;
use TicketingBundle\Entity\Order;

class MailBySwiftmailer{

    private $mailer;
    private $twig;

    public function  __construct(\Swift_Mailer $mailer,\Twig_Environment $twig){

        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function mailTickets(Order $order, String $recipient, String $from)
    {

        $body = $this->renderTemplateHTML($order);
        $part = $this->renderTemplateText($order);
        

        $message = \Swift_Message::newInstance()
            ->setSubject('ticketing.email.credential')
            ->setFrom($from)
            ->setTo($recipient)
            ->setBody($body,'text/html'
            )
            ->addPart($part,'text/plain')
            ->attach(\Swift_Attachment::fromPath('C:\xampp2\htdocs\www\Symfony\web\css\louvre.css'));
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